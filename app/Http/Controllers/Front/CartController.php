<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = CartItem::with('product.brand', 'product.vehicleModel')
            ->where('session_id', $request->session()->getId())
            ->latest()
            ->get();

        return view('front.cart.index', compact('items'));
    }

    public function store(Request $request, Product $product)
    {
        if ($product->status !== 'published') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This vehicle is not available right now.',
                ], 422);
            }

            return back()->with('error', 'This vehicle is not available right now.');
        }

        $item = CartItem::firstOrCreate(
            [
                'product_id' => $product->id,
                'session_id' => $request->session()->getId(),
            ],
            [
                'user_id' => auth()->id(),
                'quantity' => 0,
            ]
        );

        $item->increment('quantity');

        if ($request->expectsJson()) {
            $cartCount = CartItem::where('session_id', $request->session()->getId())->sum('quantity');

            return response()->json([
                'message' => 'Vehicle has been added to cart.',
                'cart_count' => $cartCount,
                'button_label' => 'Added',
            ]);
        }

        return back()->with('success', 'Vehicle has been added to cart.');
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        if ($cartItem->session_id === $request->session()->getId()) {
            $cartItem->delete();
        }

        return back()->with('success', 'Vehicle has been removed from cart.');
    }
}
