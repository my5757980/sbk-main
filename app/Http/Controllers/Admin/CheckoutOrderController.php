<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckoutOrder;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $data = CheckoutOrder::with('user', 'items.product')
            ->latest()
            ->get();

        return view('admin.orders.index', compact('data'));
    }

    public function create()
    {
        $data = new CheckoutOrder([
            'status' => 'pending',
            'order_date' => now()->toDateString(),
        ]);
        $users = User::orderBy('first_name')->get();
        $products = Product::with('brand', 'vehicleModel')->orderBy('title')->get();
        $selectedProductId = null;
        $quantity = 1;
        $form = [
            'heading' => 'Create Order',
            'method' => 'POST',
            'action' => route('admin.orders.store'),
            'cancel_url' => route('admin.orders.index'),
        ];

        return view('admin.orders.form', compact('data', 'users', 'products', 'selectedProductId', 'quantity', 'form'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated) {
            $user = User::findOrFail($validated['user_id']);
            $product = Product::with('brand', 'vehicleModel')->findOrFail($validated['product_id']);
            $quantity = (int) $validated['quantity'];
            $subtotal = $validated['amount'] * $quantity;
            $remainingAmount = max(round($subtotal - $validated['paid_amount'], 2), 0);

            $order = CheckoutOrder::create([
                'user_id' => $user->id,
                'order_no' => 'SBK-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
                'name' => $user->name ?: trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'payment_method' => $validated['payment_method'] ?? 'Manual Admin Order',
                'message' => $validated['message'] ?? null,
                'subtotal' => $subtotal,
                'paid_amount' => $validated['paid_amount'],
                'remaining_amount' => $remainingAmount,
                'payment_percentage' => $subtotal > 0 ? min(round(($validated['paid_amount'] / $subtotal) * 100), 100) : 0,
                'payment_status' => $remainingAmount > 0 ? 'partial' : 'paid',
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
            ]);

            $this->syncOrderItem($order, $product, $quantity, $validated['amount']);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Order has been created successfully.');
    }

    public function show($id)
    {
        $data = CheckoutOrder::with('user', 'items.product.brand', 'items.product.vehicleModel')->findOrFail($id);

        return view('admin.orders.show', compact('data'));
    }

    public function edit($id)
    {
        $data = CheckoutOrder::with('items')->findOrFail($id);
        $users = User::orderBy('first_name')->get();
        $products = Product::with('brand', 'vehicleModel')->orderBy('title')->get();
        $firstItem = $data->items->first();
        $selectedProductId = old('product_id', optional($firstItem)->product_id);
        $quantity = old('quantity', optional($firstItem)->quantity ?? 1);
        $form = [
            'heading' => 'Edit Order',
            'method' => 'PUT',
            'action' => route('admin.orders.update', $id),
            'cancel_url' => route('admin.orders.index'),
        ];

        return view('admin.orders.form', compact('data', 'users', 'products', 'selectedProductId', 'quantity', 'form'));
    }

    public function update(Request $request, $id)
    {
        $order = CheckoutOrder::findOrFail($id);
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($order, $validated) {
            $user = User::findOrFail($validated['user_id']);
            $product = Product::with('brand', 'vehicleModel')->findOrFail($validated['product_id']);
            $quantity = (int) $validated['quantity'];
            $subtotal = $validated['amount'] * $quantity;
            $remainingAmount = max(round($subtotal - $validated['paid_amount'], 2), 0);

            $order->update([
                'user_id' => $user->id,
                'name' => $user->name ?: trim($user->first_name . ' ' . $user->last_name),
                'email' => $user->email,
                'phone' => $user->phone,
                'country' => $user->country,
                'payment_method' => $validated['payment_method'] ?? $order->payment_method,
                'message' => $validated['message'] ?? null,
                'subtotal' => $subtotal,
                'paid_amount' => $validated['paid_amount'],
                'remaining_amount' => $remainingAmount,
                'payment_percentage' => $subtotal > 0 ? min(round(($validated['paid_amount'] / $subtotal) * 100), 100) : 0,
                'payment_status' => $remainingAmount > 0 ? 'partial' : 'paid',
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
            ]);

            $order->items()->delete();
            $this->syncOrderItem($order, $product, $quantity, $validated['amount']);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Order has been updated successfully.');
    }

    public function destroy($id)
    {
        CheckoutOrder::findOrFail($id)->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order has been deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'order_date' => ['required', 'date'],
            'status' => ['required', 'in:pending,processing,paid,completed,cancelled'],
            'payment_method' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function syncOrderItem(CheckoutOrder $order, Product $product, int $quantity, float $amount): void
    {
        $order->items()->create([
            'product_id' => $product->id,
            'product_title' => $product->title,
            'brand' => optional($product->brand)->name,
            'model' => optional($product->vehicleModel)->name,
            'quantity' => $quantity,
            'price' => $amount,
        ]);
    }
}
