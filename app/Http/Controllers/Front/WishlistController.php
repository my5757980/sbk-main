<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = WishlistItem::with('product.brand', 'product.vehicleModel')
            ->where('session_id', $request->session()->getId())
            ->latest()
            ->get();

        return view('front.wishlist.index', compact('items'));
    }

    public function store(Request $request, Product $product)
    {
        if ($product->status !== 'published') {
            return back()->with('error', 'This vehicle is not available right now.');
        }

        WishlistItem::firstOrCreate([
            'product_id' => $product->id,
            'session_id' => $request->session()->getId(),
        ], [
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Vehicle has been added to wishlist.');
    }

    public function destroy(Request $request, WishlistItem $wishlistItem)
    {
        if ($wishlistItem->session_id === $request->session()->getId()) {
            $wishlistItem->delete();
        }

        return back()->with('success', 'Vehicle has been removed from wishlist.');
    }
}
