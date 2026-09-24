<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\CheckoutOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            if (session('success')) {
                $subtotal = 0;

                return view('front.checkout.index', compact('items', 'subtotal'));
            }

            return redirect()->route('cart.index')->with('error', 'Please add a vehicle to cart before checkout.');
        }

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);

        return view('front.checkout.index', compact('items', 'subtotal'));
    }

    public function store(Request $request)
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Please add a vehicle to cart before checkout.');
        }

        $validated = $this->validatedCheckout($request, false);
        $subtotal = $this->cartSubtotal($items);
        $payment = $this->paymentBreakdown($subtotal, (int) $validated['payment_percentage']);
        $payment['paid_amount'] = 0;
        $payment['remaining_amount'] = $subtotal;

        $order = $this->createCheckoutOrder($request, $validated, $items, $subtotal, $payment, [
            'payment_method' => 'Order Check',
            'status' => 'pending',
            'order_status' => 'pending',
        ]);

        return redirect()->route('checkout.index')->with('success', 'Order submitted successfully. Order #' . $order->order_no);
    }

    public function createPaypalOrder(Request $request)
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return response()->json(['message' => 'Please add a vehicle to cart before checkout.'], 422);
        }

        $validated = $request->validate([
            'payment_percentage' => ['required', 'integer', 'in:10,30,50,100'],
        ]);

        $subtotal = $this->cartSubtotal($items);
        $payment = $this->paymentBreakdown($subtotal, (int) $validated['payment_percentage']);
        $accessToken = $this->paypalAccessToken();

        if (!$accessToken) {
            return response()->json(['message' => 'PayPal is not configured yet.'], 422);
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($this->paypalBaseUrl() . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => 'SBK-' . now()->format('ymdHis'),
                    'description' => 'SBK Global Auto Trading vehicle payment',
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($payment['paid_amount'], 2, '.', ''),
                    ],
                ]],
            ]);

        if (!$response->successful()) {
            return response()->json(['message' => 'Unable to create PayPal order.'], 422);
        }

        return response()->json([
            'id' => $response->json('id'),
            'paid_amount' => $payment['paid_amount'],
            'remaining_amount' => $payment['remaining_amount'],
            'effective_percentage' => $payment['effective_percentage'],
        ]);
    }

    public function capturePaypalOrder(Request $request)
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return response()->json(['message' => 'Please add a vehicle to cart before checkout.'], 422);
        }

        $validated = $this->validatedCheckout($request);
        $subtotal = $this->cartSubtotal($items);
        $payment = $this->paymentBreakdown($subtotal, (int) $validated['payment_percentage']);
        $accessToken = $this->paypalAccessToken();

        if (!$accessToken) {
            return response()->json(['message' => 'PayPal is not configured yet.'], 422);
        }

        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($this->paypalBaseUrl() . '/v2/checkout/orders/' . $validated['paypal_order_id'] . '/capture', []);

        if (!$response->successful() || $response->json('status') !== 'COMPLETED') {
            return response()->json(['message' => 'PayPal payment was not completed.'], 422);
        }

        $capturedAmount = (float) ($response->json('purchase_units.0.payments.captures.0.amount.value') ?? 0);

        if ($capturedAmount + 0.01 < $payment['paid_amount']) {
            return response()->json(['message' => 'PayPal payment amount does not match the checkout amount.'], 422);
        }

        $order = $this->createCheckoutOrder($request, $validated, $items, $subtotal, $payment, $response->json());
        $request->session()->flash('success', 'Payment completed successfully. Order #' . $order->order_no);

        return response()->json([
            'message' => 'Payment completed successfully. Order #' . $order->order_no,
            'redirect_url' => route('checkout.index'),
        ]);
    }

    private function validatedCheckout(Request $request, bool $requiresPaypal = true): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192'],
            'phone' => ['required', 'string', 'max:60'],
            'country' => ['required', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'port' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],
            'payment_percentage' => ['required', 'integer', 'in:10,30,50,100'],
            'paypal_order_id' => [$requiresPaypal ? 'required' : 'nullable', 'string', 'max:255'],
        ];

        if (!Auth::check() && !User::where('email', $request->input('email'))->exists()) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $request->validate($rules);
    }

    private function createCheckoutOrder(Request $request, array $validated, $items, float $subtotal, array $payment, array $paypalPayload)
    {
        return DB::transaction(function () use ($validated, $request, $items, $subtotal, $payment, $paypalPayload) {
            $user = $this->resolveCheckoutUser($request, $validated);
            $order = CheckoutOrder::create([
                'user_id' => $user->id,
                'order_no' => 'SBK-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
                'session_id' => $request->session()->getId(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'city' => $validated['city'] ?? null,
                'port' => $validated['port'] ?? null,
                'payment_method' => $paypalPayload['payment_method'] ?? 'PayPal',
                'payment_percentage' => $payment['effective_percentage'],
                'message' => $validated['message'] ?? null,
                'subtotal' => $subtotal,
                'paid_amount' => $payment['paid_amount'],
                'remaining_amount' => $payment['remaining_amount'],
                'paypal_order_id' => $validated['paypal_order_id'] ?? null,
                'payment_status' => $paypalPayload['status'] ?? 'completed',
                'order_date' => now()->toDateString(),
                'status' => $paypalPayload['order_status'] ?? ($payment['remaining_amount'] > 0 ? 'processing' : 'paid'),
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_title' => $item->product->title,
                    'brand' => optional($item->product->brand)->name,
                    'model' => optional($item->product->vehicleModel)->name,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            CartItem::where('session_id', $request->session()->getId())->delete();

            return $order;
        });
    }

    private function resolveCheckoutUser(Request $request, array $validated): User
    {
        if ($request->user()) {
            $request->user()->update([
                'phone' => $validated['phone'],
                'country' => $validated['country'],
            ]);

            return $request->user();
        }

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            [$firstName, $lastName] = $this->splitName($validated['name']);
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'password' => Hash::make($validated['password']),
                'status' => 1,
                'role' => 'customer',
            ]);

            Auth::login($user);
            $request->session()->regenerate();
        } else {
            $user->update([
                'phone' => $validated['phone'],
                'country' => $validated['country'],
            ]);

            if (!Auth::check() && isset($validated['password']) && Hash::check($validated['password'], $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
            }
        }

        return $user;
    }

    private function cartItems(Request $request)
    {
        return CartItem::with('product.brand', 'product.vehicleModel')
            ->where('session_id', $request->session()->getId())
            ->get()
            ->filter(fn ($item) => $item->product);
    }

    private function cartSubtotal($items): float
    {
        return (float) $items->sum(fn ($item) => $item->product->price * $item->quantity);
    }

    private function paymentBreakdown(float $subtotal, int $selectedPercentage): array
    {
        $effectivePercentage = $subtotal <= 3000 && $selectedPercentage < 25
            ? 25
            : $selectedPercentage;
        $paidAmount = round($subtotal * ($effectivePercentage / 100), 2);

        return [
            'selected_percentage' => $selectedPercentage,
            'effective_percentage' => $effectivePercentage,
            'paid_amount' => $paidAmount,
            'remaining_amount' => max(round($subtotal - $paidAmount, 2), 0),
        ];
    }

    private function paypalAccessToken(): ?string
    {
        $clientId = config('services.paypal.client_id');
        $secret = config('services.paypal.secret');

        if (!$clientId || !$secret) {
            return null;
        }

        $response = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($this->paypalBaseUrl() . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        return $response->successful() ? $response->json('access_token') : null;
    }

    private function paypalBaseUrl(): string
    {
        return config('services.paypal.mode') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }
}
