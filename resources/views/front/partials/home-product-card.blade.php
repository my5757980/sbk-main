<article class="sbk-car rail-item" data-card-url="{{ route('product.show', $product) }}">
    <div class="sbk-car__media">
        <img src="{{ $product->primary_image_url }}" alt="{{ $product->title }}">
        @if($product->tag)<span class="sbk-car__tag">{{ $product->tag }}</span>@endif
        <form action="{{ route('wishlist.store', $product) }}" method="POST">
            @csrf
            <button class="sbk-car__fav" type="submit" title="Add to wishlist">{!! in_array($product->id, $wishlistProductIds ?? [], true) ? '&hearts;' : '&#9825;' !!}</button>
        </form>
    </div>
    <div class="sbk-car__body">
        <span class="sbk-car__brand">{{ optional($product->brand)->name }}</span>
        <h3><a href="{{ route('product.show', $product) }}">{{ $product->title }}</a></h3>
        <div class="sbk-car__specs">
            <span>{{ $product->year ?: 'N/A' }}</span>
            <span>{{ $product->mileage_label }}</span>
            <span>{{ $product->fuel_type ?: 'N/A' }}</span>
        </div>
        <div class="sbk-car__foot">
            <div class="sbk-price"><small>FOB Price</small>@if($product->discounted_price !== null && $product->discounted_price < $product->price)<del style="display:block;font-size:14px;opacity:.65">${{ number_format($product->price, 2) }}</del><strong style="display:block;font-weight:900">${{ number_format($product->discounted_price, 2) }}</strong>@else<strong>${{ number_format($product->price, 2) }}</strong>@endif</div>
            <div class="sbk-actions">
                <form action="{{ route('cart.store', $product) }}" method="POST" data-cart-form>
                    @csrf
                    <button class="sbk-action-btn sbk-action-btn--red" type="submit">{{ in_array($product->id, $cartProductIds ?? [], true) ? 'Added' : 'Add Cart' }}</button>
                </form>
            </div>
        </div>
    </div>
</article>
