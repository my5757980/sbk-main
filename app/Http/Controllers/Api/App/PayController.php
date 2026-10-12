<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\App\AppPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Paying by PayPal or card from the SBK app - the website checkout's own
 * PayPal payment (Front\CheckoutController@createPaypalOrder and
 * capturePaypalOrder), the same PayPal account, the same amounts.
 *
 * 1. The app sends the checkout form (start). The vehicles, the amounts and
 *    the customer are kept for 30 minutes under a one-time key, and the app
 *    opens /app/pay/{key} in the phone's browser.
 * 2. That page shows the website's PayPal and card buttons; PayPal's own
 *    screens take the payment (create, capture).
 * 3. On capture the order is written like the website's, and the page sends
 *    the phone back to the app (sbkapp://pay-result?...).
 */
class PayController extends Controller
{
    private const MINUTES = 30;

    public function start(Request $request)
    {
        if (!self::configured()) {
            return response()->json(['message' => 'PayPal is not configured yet.'], 422);
        }

        $user = auth('sanctum')->user();
        $validated = $request->validate(OrderController::rules($request, $user));
        $products = OrderController::products($validated['items']);
        if ($products->isEmpty()) {
            return response()->json(['message' => 'These vehicles are no longer listed. Please choose again from the shop.'], 422);
        }

        $subtotal = (float) $products->sum('price');
        $payment = OrderController::paymentBreakdown($subtotal, (float) $validated['payment_percentage'],
            isset($validated['payment_amount']) ? (float) $validated['payment_amount'] : null);

        // The account first, as the order will be theirs (and a new customer's
        // phone is signed in now, whether or not they finish paying).
        $token = null;
        // A full closure: an arrow function would take $token by value and lose the sign-in.
        $customer = DB::transaction(function () use ($validated, $user, $request, &$token) {
            return OrderController::customer($validated, $user, $request, $token);
        });

        $key = Str::random(40);
        unset($validated['password'], $validated['password_confirmation']);
        Cache::put(self::cacheKey($key), [
            'user_id' => $customer->id,
            'validated' => $validated,
            'slugs' => $products->pluck('slug')->all(),
            'subtotal' => $subtotal,
            'payment' => $payment,
        ], now()->addMinutes(self::MINUTES));

        return response()->json([
            'url' => url('/app/pay/' . $key),
            'returnUrl' => 'sbkapp://pay-result',
            'payNow' => $payment['paid_amount'],
            'signedIn' => $token,
        ]);
    }

    /** The payment page the phone's browser opens. */
    public function page(string $key)
    {
        $pay = Cache::get(self::cacheKey($key));
        $products = $pay ? OrderController::products($pay['slugs']) : collect();

        return response()->view('app.pay', [
            'key' => $key,
            'pay' => $pay,
            'products' => $products,
            'clientId' => config('services.paypal.client_id'),
        ])->header('Cache-Control', 'no-store');
    }

    /** PayPal's order for the pay-now amount (createPaypalOrder). */
    public function create(string $key)
    {
        $pay = Cache::get(self::cacheKey($key));
        if (!$pay) {
            return response()->json(['message' => 'This payment page has expired. Please go back to the app and try again.'], 410);
        }
        $accessToken = self::accessToken();
        if (!$accessToken) {
            return response()->json(['message' => 'PayPal is not configured yet.'], 422);
        }

        $response = Http::withToken($accessToken)->acceptJson()->post(self::baseUrl() . '/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => 'SBK-' . now()->format('ymdHis'),
                'description' => 'SBK Global Auto Trading vehicle payment',
                'amount' => ['currency_code' => 'USD', 'value' => number_format($pay['payment']['paid_amount'], 2, '.', '')],
            ]],
        ]);
        if (!$response->successful()) {
            return response()->json(['message' => 'Unable to create PayPal order.'], 422);
        }

        return response()->json(['id' => $response->json('id')]);
    }

    /** The payment taken: the order is written as the website writes it (capturePaypalOrder). */
    public function capture(Request $request, string $key)
    {
        // One capture at a time for a key: a double tap must not write two orders.
        $lock = Cache::lock(self::cacheKey($key) . ':lock', 60);
        if (!$lock->get()) {
            return response()->json(['message' => 'This payment is already being completed.'], 409);
        }
        try {
            return $this->captureLocked($request, $key);
        } finally {
            $lock->release();
        }
    }

    private function captureLocked(Request $request, string $key)
    {
        $pay = Cache::get(self::cacheKey($key));
        if (!$pay) {
            return response()->json(['message' => 'This payment page has expired. Please go back to the app and try again.'], 410);
        }
        $orderId = (string) $request->input('orderID', '');
        if ($orderId === '' || strlen($orderId) > 255) {
            return response()->json(['message' => 'PayPal payment was not completed.'], 422);
        }
        $accessToken = self::accessToken();
        if (!$accessToken) {
            return response()->json(['message' => 'PayPal is not configured yet.'], 422);
        }

        $response = Http::withToken($accessToken)->acceptJson()
            ->post(self::baseUrl() . '/v2/checkout/orders/' . rawurlencode($orderId) . '/capture', []);
        if (!$response->successful() || $response->json('status') !== 'COMPLETED') {
            return response()->json(['message' => 'PayPal payment was not completed.'], 422);
        }
        $captured = (float) ($response->json('purchase_units.0.payments.captures.0.amount.value') ?? 0);
        if ($captured + 0.01 < $pay['payment']['paid_amount']) {
            return response()->json(['message' => 'PayPal payment amount does not match the checkout amount.'], 422);
        }

        // Used once: a second capture with the same key writes nothing.
        Cache::forget(self::cacheKey($key));

        $customer = User::findOrFail($pay['user_id']);
        $products = OrderController::products($pay['slugs']);
        $payment = $pay['payment'];
        try {
            $order = DB::transaction(fn () => OrderController::createOrder($customer, $pay['validated'], $products, (float) $pay['subtotal'], $payment, [
                'payment_method' => 'PayPal',
                'paid_amount' => $payment['paid_amount'],
                'remaining_amount' => $payment['remaining_amount'],
                'paypal_order_id' => $orderId,
                'payment_status' => (string) ($response->json('status') ?? 'completed'),
                'status' => $payment['remaining_amount'] > 0 ? 'processing' : 'paid',
            ]));
        } catch (\Throwable $e) {
            // The money is taken: this must never be lost in silence.
            Log::error('App PayPal payment captured but the order could not be written', [
                'paypal_order_id' => $orderId, 'user_id' => $pay['user_id'], 'amount' => $captured, 'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Your payment went through, but the order could not be saved. SBK has been told and will contact you. PayPal reference: ' . $orderId], 500);
        }

        return response()->json([
            'message' => 'Payment completed successfully. Order #' . $order->order_no,
            'order' => AppPresenter::order($order->load('items.product.brand', 'items.product.vehicleModel')),
            'redirect' => 'sbkapp://pay-result?status=paid&order=' . rawurlencode($order->order_no),
        ]);
    }

    private static function cacheKey(string $key): string
    {
        return 'app-pay:' . preg_replace('/[^A-Za-z0-9]/', '', $key);
    }

    private static function configured(): bool
    {
        return (bool) (config('services.paypal.client_id') && config('services.paypal.secret'));
    }

    /** Front\CheckoutController::paypalAccessToken. */
    private static function accessToken(): ?string
    {
        if (!self::configured()) {
            return null;
        }
        $response = Http::asForm()
            ->withBasicAuth(config('services.paypal.client_id'), config('services.paypal.secret'))
            ->post(self::baseUrl() . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        return $response->successful() ? $response->json('access_token') : null;
    }

    /** Front\CheckoutController::paypalBaseUrl. */
    private static function baseUrl(): string
    {
        return config('services.paypal.mode') === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com';
    }
}
