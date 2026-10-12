<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\CheckoutOrder;
use App\Models\ContactUs;
use App\Models\Product;
use App\Models\User;
use App\Services\App\AppPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Checkout and the contact form, from the SBK app, into this website's
 * database - the website's own "Submit Order for Checking"
 * (Front\CheckoutController@store) and contact form (SiteController@submitContact).
 * The cart is on the phone, so the vehicles come with the order.
 */
class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $user = auth('sanctum')->user();
        $validated = $request->validate(self::rules($request, $user));

        $products = self::products($validated['items']);
        if ($products->isEmpty()) {
            return response()->json(['message' => 'These vehicles are no longer listed. Please choose again from the shop.'], 422);
        }

        $subtotal = (float) $products->sum('price');
        $payment = self::paymentBreakdown($subtotal, (float) $validated['payment_percentage'],
            isset($validated['payment_amount']) ? (float) $validated['payment_amount'] : null);

        $token = null;
        $order = DB::transaction(function () use ($validated, $products, $subtotal, $payment, $user, $request, &$token) {
            $customer = self::customer($validated, $user, $request, $token);

            // The website's "Submit Order for Checking": nothing paid yet.
            return self::createOrder($customer, $validated, $products, $subtotal, $payment, [
                'payment_method' => 'Order Check',
                'paid_amount' => 0,
                'remaining_amount' => $subtotal,
                'paypal_order_id' => null,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);
        });

        return response()->json([
            'message' => 'Order submitted successfully. Order #' . $order->order_no,
            'order' => AppPresenter::order($order->load('items.product.brand', 'items.product.vehicleModel')),
            'payNow' => $payment['paid_amount'],
            'signedIn' => $token,
        ], 201);
    }

    /** Front\CheckoutController::validatedCheckout's rules, with the phone's cart. */
    public static function rules(Request $request, ?User $user): array
    {
        $rules = [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*' => ['required', 'string', 'max:191'],
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192'],
            'phone' => ['required', 'string', 'max:60'],
            'country' => ['required', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'port' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],
            'payment_percentage' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'payment_amount' => ['nullable', 'numeric', 'min:0.01'],
        ];
        // As on the website: a new customer chooses a password, and the order makes the account.
        if (!$user && !User::where('email', $request->input('email'))->exists()) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /** The listed vehicles among the phone's cart. */
    public static function products(array $slugs)
    {
        return Product::with(['brand', 'vehicleModel'])->published()->whereIn('slug', array_unique($slugs))->get();
    }

    /**
     * Who the order belongs to (Front\CheckoutController::resolveCheckoutUser):
     * the signed-in customer; else the account with that email (signed in only
     * with its own password); else a new account, signed in. $token is set when
     * the phone gets signed in.
     */
    public static function customer(array $validated, ?User $user, Request $request, ?array &$token): User
    {
        if ($user) {
            $user->update(['phone' => $validated['phone'], 'country' => $validated['country']]);

            return $user;
        }

        $customer = User::where('email', $validated['email'])->first();
        if (!$customer) {
            [$firstName, $lastName] = AccountController::splitName($validated['name']);
            $customer = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'],
                'password' => Hash::make($validated['password']),
                'status' => 1,
                'role' => 'customer',
            ]);
            $token = AccountController::signedIn($customer, $request);
        } elseif (isset($validated['password']) && Hash::check($validated['password'], $customer->password)) {
            $customer->update(['phone' => $validated['phone'], 'country' => $validated['country']]);
            $token = AccountController::signedIn($customer, $request);
        }

        return $customer;
    }

    /** Front\CheckoutController::createCheckoutOrder, with the vehicles from the phone. */
    public static function createOrder(User $customer, array $validated, $products, float $subtotal, array $payment, array $how): CheckoutOrder
    {
        $order = CheckoutOrder::create([
            'user_id' => $customer->id,
            'order_no' => 'SBK-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
            'session_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'city' => $validated['city'] ?? null,
            'port' => $validated['port'] ?? null,
            'payment_method' => $how['payment_method'],
            'payment_percentage' => (int) round($payment['effective_percentage']),
            'message' => $validated['message'] ?? null,
            'subtotal' => $subtotal,
            'paid_amount' => $how['paid_amount'],
            'remaining_amount' => $how['remaining_amount'],
            'paypal_order_id' => $how['paypal_order_id'],
            'payment_status' => $how['payment_status'],
            'order_date' => now()->toDateString(),
            'status' => $how['status'],
        ]);

        foreach ($products as $product) {
            $order->items()->create([
                'product_id' => $product->id,
                'product_title' => $product->title,
                'brand' => optional($product->brand)->name,
                'model' => optional($product->vehicleModel)->name,
                'quantity' => 1,
                'price' => $product->price,
            ]);
        }

        return $order;
    }

    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192'],
            'phone' => ['nullable', 'string', 'max:192'],
            'subject' => ['nullable', 'string', 'max:192'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $nameParts = preg_split('/\s+/', trim($validated['name']), 2);
        $message = trim($validated['message'] ?? '');
        if (!empty($validated['subject'])) {
            $message = 'Subject: ' . $validated['subject'] . ($message ? "\n\n" . $message : '');
        }

        ContactUs::create([
            'first_name' => $nameParts[0] ?? $validated['name'],
            'last_name' => $nameParts[1] ?? '-',
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? '-',
            'message' => $message,
        ]);

        return response()->json(['message' => 'Thank you. Your message has been submitted successfully.'], 201);
    }

    /** Front\CheckoutController::paymentBreakdown, line for line. */
    public static function paymentBreakdown(float $subtotal, float $selectedPercentage, ?float $selectedAmount = null): array
    {
        $selectedPercentage = min(max($selectedPercentage, 0.01), 100);
        $selectedAmount = $selectedAmount === null ? null : min(max($selectedAmount, 0.01), $subtotal);
        $minimumPercentage = $subtotal <= 3000 ? 25 : 0.01;

        if ($selectedAmount !== null) {
            $effectivePercentage = $subtotal > 0 ? ($selectedAmount / $subtotal) * 100 : 0;
            if ($minimumPercentage && $effectivePercentage < $minimumPercentage) {
                $effectivePercentage = $minimumPercentage;
                $paidAmount = round($subtotal * ($effectivePercentage / 100), 2);
            } else {
                $paidAmount = round($selectedAmount, 2);
            }
        } else {
            $effectivePercentage = $minimumPercentage && $selectedPercentage < $minimumPercentage
                ? $minimumPercentage
                : $selectedPercentage;
            $paidAmount = round($subtotal * ($effectivePercentage / 100), 2);
        }

        return [
            'selected_percentage' => $selectedPercentage,
            'effective_percentage' => $effectivePercentage,
            'paid_amount' => $paidAmount,
            'remaining_amount' => max(round($subtotal - $paidAmount, 2), 0),
        ];
    }
}
