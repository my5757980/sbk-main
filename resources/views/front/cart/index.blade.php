@extends('front.layouts.app')

@php $assetBase = asset('assets/front'); @endphp

@section('content')
<style>
.sbk-list{padding:70px 0 100px;background:#fff;color:#111}.sbk-list .container{width:min(1120px,calc(100% - 36px));max-width:none!important;margin:auto}.sbk-list-head{display:flex;justify-content:space-between;gap:20px;align-items:center;margin-bottom:24px}.sbk-list h1{margin:0;color:#b40000;font-size:42px}.sbk-checkout-link{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 22px;border-radius:7px;background:#d71920;color:#fff!important;font-weight:900;text-decoration:none}.sbk-list-alert{margin-bottom:18px;padding:13px 15px;border-radius:7px;background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-list-grid{display:grid;gap:16px}.sbk-list-item{display:grid;grid-template-columns:150px 1fr auto;gap:20px;align-items:center;border:1px solid #e8e8e8;border-radius:8px;padding:14px;background:#fff;box-shadow:0 10px 28px rgba(0,0,0,.06)}.sbk-list-item img{width:150px;height:100px;object-fit:cover;border-radius:6px}.sbk-list-item h3{margin:0 0 8px;font-size:20px}.sbk-list-item p{margin:0;color:#666}.sbk-list-price{font-weight:900;font-size:18px}.sbk-list-actions{display:flex;gap:10px;align-items:center}.sbk-list-actions button,.sbk-list-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 14px;border:0;border-radius:6px;background:#111;color:#fff;font-weight:800;cursor:pointer}.sbk-list-actions button{background:#d71920}.sbk-list-empty{padding:40px;border:1px dashed #ddd;border-radius:8px;text-align:center}.sbk-list-empty a{color:#d71920;font-weight:900}@media(max-width:700px){.sbk-list-head{align-items:flex-start;flex-direction:column}.sbk-list-item{grid-template-columns:1fr}.sbk-list-item img{width:100%;height:210px}.sbk-list-actions{justify-content:flex-start;flex-wrap:wrap}}
</style>
<section class="sbk-list">
    <div class="container">
        <div class="sbk-list-head">
            <h1>Your Cart</h1>
            @if($items->count())
                <a class="sbk-checkout-link" href="{{ route('checkout.index') }}">Proceed To Checkout</a>
            @endif
        </div>
        @if(session('error'))
            <div class="sbk-list-alert">{{ session('error') }}</div>
        @endif
        @if($items->count())
            <div class="sbk-list-grid">
                @foreach($items as $item)
                    <article class="sbk-list-item">
                        <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->title }}">
                        <div>
                            <h3>{{ $item->product->title }}</h3>
                            <p>{{ optional($item->product->brand)->name }} / {{ optional($item->product->vehicleModel)->name }} / Qty: {{ $item->quantity }}</p>
                        </div>
                        <div class="sbk-list-actions">
                            <span class="sbk-list-price">${{ number_format($item->product->price, 2) }}</span>
                            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Remove</button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="sbk-list-empty">Your cart is empty. <a href="{{ route('shop') }}">Browse vehicles</a></div>
        @endif
    </div>
</section>
@endsection
