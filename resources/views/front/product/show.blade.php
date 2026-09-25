@extends('front.layouts.app')

@php
$assetBase = asset('assets/front');
$images = $product->images ?: [];
$mainImage = $product->primary_image_url;
$metaTitle = $product->meta_title ?: $product->title . ' | SBK Global Auto Trading';
$metaDescription = $product->meta_description ?: ($product->short_description ?: 'View details, specifications, price and export inquiry options for ' . $product->title . '.');
$specs = [
    'Brand' => optional($product->brand)->name,
    'Model' => optional($product->vehicleModel)->name,
    'Year' => $product->year,
    'Mileage' => $product->mileage_label,
    'Fuel' => $product->fuel_type,
    'Transmission' => $product->transmission,
    'Body Type' => $product->body_type,
    'Engine' => $product->engine,
    'Color' => $product->color,
    'Steering' => $product->steering,
    'Location' => $product->location,
    'Stock No' => $product->stock_no,
    'Chassis No' => $product->chassis_no,
];

foreach (($product->vehicle_specifications ?: []) as $specification) {
    $name = trim((string) ($specification['name'] ?? ''));
    $value = trim((string) ($specification['value'] ?? ''));

    if ($name !== '' || $value !== '') {
        $specs[$name ?: 'Specification'] = $value;
    }
}
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDescription)
@section('meta_keywords', $product->meta_keywords)

@section('content')
<style>
.sbk-product{--red:#d71920;--red-dark:#a90c12;--dark:#07080a;--ink:#111216;--muted:#69707a;--line:#e7e8eb;--soft:#f6f7f8;background:#fff;color:var(--ink);overflow:hidden}.sbk-product *{box-sizing:border-box}.sbk-product .container{width:min(1240px,calc(100% - 40px));max-width:none!important;margin:auto}.sbk-product-hero{position:relative;isolation:isolate;padding:82px 0 116px;background:#07080a;color:#fff}.sbk-product-hero:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.96),rgba(0,0,0,.72) 48%,rgba(0,0,0,.22)),url("{{ $mainImage }}") center/cover no-repeat;filter:saturate(.92) contrast(1.05)}.sbk-product-hero:after{content:"SBK";position:absolute;right:-24px;bottom:-86px;z-index:-1;color:rgba(255,255,255,.06);font:900 230px/1 Arial,sans-serif;letter-spacing:-18px}.sbk-product-crumb{display:flex;gap:10px;align-items:center;margin-bottom:18px;color:rgba(255,255,255,.62);font-size:12px;font-weight:800;text-transform:uppercase}.sbk-product-crumb b{color:#fff}.sbk-product-kicker{display:inline-flex;align-items:center;gap:12px;color:#ff454d;font-size:12px;font-weight:900;letter-spacing:2px;text-transform:uppercase}.sbk-product-kicker:before{content:"";width:42px;height:3px;background:var(--red)}.sbk-product-hero h1{max-width:830px;margin:16px 0 16px;color:#fff;font-size:clamp(42px,6vw,78px);line-height:.96;letter-spacing:0}.sbk-product-hero p{max-width:660px;margin:0;color:rgba(255,255,255,.78);font-size:16px;line-height:1.75}.sbk-hero-facts{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}.sbk-hero-facts span{display:inline-flex;align-items:center;min-height:42px;padding:0 16px;border:1px solid rgba(255,255,255,.18);border-radius:6px;background:rgba(255,255,255,.08);backdrop-filter:blur(10px);color:#fff;font-size:12px;font-weight:900;text-transform:uppercase}.sbk-product-main{position:relative;margin-top:-70px;padding:0 0 92px}.sbk-product-grid{display:grid;grid-template-columns:minmax(0,1.15fr) 410px;gap:30px;align-items:start}.sbk-gallery{display:grid;gap:14px}.sbk-gallery-shell{position:relative;padding:12px;border:1px solid rgba(255,255,255,.24);border-radius:12px;background:#fff;box-shadow:0 28px 75px rgba(0,0,0,.18)}.sbk-gallery-main{position:relative;overflow:hidden;border-radius:9px;background:#f1f2f4}.sbk-gallery-main:before{content:"Verified Stock";position:absolute;left:18px;top:18px;z-index:2;padding:8px 11px;border-radius:5px;background:var(--red);color:#fff;font-size:10px;font-weight:900;letter-spacing:1px;text-transform:uppercase}.sbk-gallery-main img{display:block;width:100%;height:550px;object-fit:cover}.sbk-gallery-thumbs{display:grid;grid-template-columns:repeat(5,1fr);gap:10px}.sbk-gallery-thumbs button{height:92px;padding:0;border:2px solid transparent;border-radius:8px;overflow:hidden;background:#f4f4f4;cursor:pointer;box-shadow:0 9px 20px rgba(0,0,0,.06);transition:border-color .2s ease,transform .2s ease}.sbk-gallery-thumbs button:hover,.sbk-gallery-thumbs button.is-active{border-color:var(--red);transform:translateY(-2px)}.sbk-gallery-thumbs img{width:100%;height:100%;object-fit:cover}.sbk-buybox{position:sticky;top:18px;border:1px solid rgba(0,0,0,.08);border-radius:12px;background:#fff;box-shadow:0 28px 70px rgba(17,18,20,.16);overflow:hidden}.sbk-buybox-head{position:relative;padding:28px;background:linear-gradient(135deg,#15161a,#050505);color:#fff;overflow:hidden}.sbk-buybox-head:after{content:"";position:absolute;right:-42px;top:-42px;width:145px;height:145px;border:28px solid rgba(215,25,32,.22);border-radius:50%}.sbk-buybox-head span{display:block;color:#ff4a52;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:1.6px}.sbk-buybox-head strong{position:relative;z-index:1;display:block;margin-top:9px;color:#fff;font-size:38px;line-height:1}.sbk-buybox-head small{position:relative;z-index:1;display:block;margin-top:9px;color:rgba(255,255,255,.64);font-size:12px}.sbk-buybox-body{padding:24px}.sbk-buy-actions{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px}.sbk-buy-actions form{margin:0}.sbk-buy-actions button,.sbk-buy-actions a{display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:50px;border:0;border-radius:7px;background:#111;color:#fff;font-size:12px;font-weight:900;text-transform:uppercase;cursor:pointer;text-decoration:none;transition:transform .2s ease,box-shadow .2s ease,background .2s ease}.sbk-buy-actions button:hover,.sbk-buy-actions a:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(0,0,0,.13)}.sbk-buy-actions button:first-child{background:var(--red)}.sbk-buy-actions button:first-child:hover{background:var(--red-dark)}.sbk-buy-actions .wide{grid-column:1/-1;background:#f1f2f4;color:#111}.sbk-mini-specs{display:grid;gap:0;border:1px solid #eee;border-radius:9px;overflow:hidden;background:#fff}.sbk-mini-specs div{display:flex;justify-content:space-between;gap:16px;padding:13px 15px;border-bottom:1px solid #eee;color:#666;font-size:13px}.sbk-mini-specs div:last-child{border-bottom:0}.sbk-mini-specs span{font-weight:800;text-transform:uppercase;font-size:11px;letter-spacing:.7px}.sbk-mini-specs b{color:#111;text-align:right}.sbk-buy-note{margin-top:16px;padding:14px;border-radius:8px;background:#fff4f4;color:#7a1b1f;font-size:12px;line-height:1.55}.sbk-section{margin-top:30px;border:1px solid var(--line);border-radius:12px;background:#fff;box-shadow:0 18px 45px rgba(17,18,20,.08);overflow:hidden}.sbk-section h2{display:flex;align-items:center;gap:12px;margin:0;padding:22px 24px;border-bottom:1px solid var(--line);font-size:24px;line-height:1.15}.sbk-section h2:before{content:"";width:6px;height:28px;border-radius:8px;background:var(--red)}.sbk-section-body{padding:24px;color:#3d424a;line-height:1.75}.sbk-spec-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px}.sbk-spec-card{position:relative;min-height:96px;padding:18px 16px 16px;border:1px solid #ececef;border-radius:9px;background:linear-gradient(180deg,#fff,#fafafa);overflow:hidden}.sbk-spec-card:before{content:"";position:absolute;left:0;top:0;width:100%;height:3px;background:var(--red);opacity:.9}.sbk-spec-card span{display:block;color:#777;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.75px}.sbk-spec-card b{display:block;margin-top:8px;color:#111;font-size:16px;line-height:1.25}.sbk-description{font-size:15px}.sbk-related{padding:0 0 96px}.sbk-related h2{margin:0 0 22px;font-size:32px}.sbk-related-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.sbk-related-card{overflow:hidden;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 16px 38px rgba(17,18,20,.08);transition:transform .25s ease,box-shadow .25s ease}.sbk-related-card:hover{transform:translateY(-5px);box-shadow:0 24px 55px rgba(17,18,20,.13)}.sbk-related-card img{width:100%;height:200px;object-fit:cover}.sbk-related-card div{padding:18px}.sbk-related-card h3{margin:0 0 8px;font-size:18px;line-height:1.25}.sbk-related-card p{margin:0 0 10px;font-weight:900}.sbk-related-card a{font-weight:900;color:var(--red)}.sbk-alert{margin:0 0 22px;padding:13px 15px;border-radius:7px;background:#edf9f1;color:#126a31;border:1px solid #bde7ca}@media(max-width:980px){.sbk-product-hero{padding-bottom:94px}.sbk-product-grid{grid-template-columns:1fr}.sbk-buybox{position:static}.sbk-spec-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:620px){.sbk-product .container{width:calc(100% - 28px)}.sbk-product-hero{padding:58px 0 92px}.sbk-product-hero:after{font-size:86px;right:-18px;bottom:-30px;letter-spacing:-6px}.sbk-hero-facts span{width:100%;justify-content:center}.sbk-product-main{margin-top:-46px}.sbk-gallery-shell{padding:8px}.sbk-gallery-main img{height:330px}.sbk-gallery-thumbs{grid-template-columns:repeat(3,1fr)}.sbk-gallery-thumbs button{height:82px}.sbk-buy-actions{grid-template-columns:1fr}.sbk-spec-grid,.sbk-related-grid{grid-template-columns:1fr}.sbk-section h2{font-size:21px}.sbk-buybox-head strong{font-size:32px}}
.sbk-description-section{position:relative;isolation:isolate;background:#f8f9fb}
.sbk-description-section:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(255,255,255,.94),rgba(255,255,255,.86)),url("{{ $mainImage }}") center/cover no-repeat;filter:grayscale(1);opacity:.86}
.sbk-description-section:after{content:"DETAILS";position:absolute;left:50%;top:16px;z-index:-1;transform:translateX(-50%);color:rgba(17,18,22,.045);font:900 clamp(76px,14vw,170px)/1 Arial,sans-serif;letter-spacing:0}
.sbk-description-section h2{justify-content:center;border-bottom:0;color:#7f0508;font-size:clamp(30px,4vw,42px);font-weight:900;text-align:center}
.sbk-description-section h2:before{display:none}
.sbk-description{position:relative;max-width:960px;margin:0 auto 18px;border:1px solid rgba(17,18,22,.06);border-radius:8px;background:rgba(255,255,255,.9);box-shadow:0 18px 35px rgba(17,18,22,.12);color:#111216;font-size:16px;font-weight:500}
.sbk-description-copy{position:relative;z-index:1;width:100%}
.sbk-description-copy p:last-child{margin-bottom:0}
.sbk-description-copy ul,.sbk-description-copy ol{padding-left:20px}
.sbk-product-content{position:relative;isolation:isolate;padding:16px;border-radius:14px;overflow:hidden}
.sbk-product-content:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(255,255,255,.9),rgba(255,255,255,.82)),url("{{ $mainImage }}") center/cover no-repeat;filter:grayscale(1) contrast(1.12);opacity:.9}
.sbk-product-content:after{content:"SBK AUTO";position:absolute;left:-14px;top:22px;z-index:-1;color:rgba(17,18,22,.045);font:900 clamp(70px,10vw,132px)/.9 Arial,sans-serif;letter-spacing:0;white-space:nowrap}
.sbk-product-content .sbk-section{background:rgba(255,255,255,.88);backdrop-filter:blur(3px)}
.sbk-product-content .sbk-section:not(.sbk-description-section){position:relative;isolation:isolate}
.sbk-product-content .sbk-section:not(.sbk-description-section):after{content:"";position:absolute;right:-34px;bottom:-58px;z-index:-1;width:330px;height:220px;background:url("{{ $mainImage }}") center/cover no-repeat;filter:grayscale(1) contrast(1.25);opacity:.08;transform:rotate(-5deg)}
.sbk-product-content .sbk-section{border:0;background:transparent;box-shadow:none;backdrop-filter:none}
.sbk-product-content .sbk-section h2{border-bottom:0;color:#111216;text-shadow:0 1px 12px rgba(255,255,255,.95)}
.sbk-product-content .sbk-section-body{color:#07080a}
.sbk-product-content .sbk-spec-card{border:0;background:transparent;box-shadow:none;text-shadow:0 1px 10px rgba(255,255,255,.95)}
.sbk-product-content .sbk-spec-card span{color:#3d424a}
.sbk-product-content .sbk-spec-card b{color:#000}
.sbk-product-content .sbk-description{border:0;background:transparent;box-shadow:none;color:#000;text-shadow:0 1px 12px rgba(255,255,255,.96)}
.sbk-description-section:before{display:none}
.sbk-description-section{background:transparent}
.sbk-description-section .sbk-description{background:transparent!important}
.sbk-description-section .sbk-description{background:rgba(255,255,255,.78)!important}
.sbk-product-grid{position:relative;isolation:isolate;padding:18px;border-radius:16px;overflow:hidden;background-image:linear-gradient(180deg,rgba(255,255,255,.9),rgba(255,255,255,.8)),radial-gradient(ellipse at center 68%,rgba(255,255,255,0) 0%,rgba(255,255,255,0) 42%,rgba(255,255,255,.92) 68%,rgba(255,255,255,.96) 100%),url("{{ $mainImage }}");background-position:center center,center center,center 68%;background-size:100% 100%,100% 100%,auto 38%;background-repeat:no-repeat}
.sbk-product-grid:after{content:"SBK STOCK";position:absolute;left:24px;top:34px;z-index:-1;color:rgba(17,18,22,.04);font:900 clamp(76px,11vw,150px)/.9 Arial,sans-serif;letter-spacing:0;white-space:nowrap}
.sbk-product-content{padding:0;background:transparent}
.sbk-product-content:before{display:none}
.sbk-description-section:after,.sbk-product-content:after,.sbk-product-content .sbk-section:not(.sbk-description-section):after,.sbk-product-grid:after{display:none}
@media(max-width:620px){.sbk-description-section:after{top:36px;font-size:70px}.sbk-description-section h2{text-align:left;justify-content:flex-start}.sbk-description{margin-bottom:8px}}
</style>

<section class="sbk-product">
    <div class="sbk-product-hero">
        <div class="container">
            <div class="sbk-product-crumb"><b>Home</b><span>/</span><b>Shop</b><span>/</span><span>Vehicle Detail</span></div>
            <span class="sbk-product-kicker">{{ optional($product->brand)->name }} / {{ optional($product->vehicleModel)->name }}</span>
            <h1>{{ $product->title }}</h1>
            <p>{{ $product->short_description ?: 'Review this vehicle, check its specifications, save it to your wishlist, or send SBK a purchase inquiry.' }}</p>
            <div class="sbk-hero-facts">
                <span>{{ $product->year ?: 'Year N/A' }}</span>
                <span>{{ $product->mileage_label }}</span>
                <span>{{ $product->fuel_type ?: 'Fuel N/A' }}</span>
                <span>{{ $product->location ?: 'Japan Stock' }}</span>
            </div>
        </div>
    </div>

    <div class="container sbk-product-main">
        @if(session('success'))
            <div class="sbk-alert">{{ session('success') }}</div>
        @endif
        <div class="sbk-product-grid">
            <div class="sbk-product-content">
                <div class="sbk-gallery">
                    <div class="sbk-gallery-shell">
                        <div class="sbk-gallery-main">
                            <img id="sbkMainProductImage" src="{{ $mainImage }}" alt="{{ $product->title }}">
                        </div>
                    </div>
                    @if(count($images) > 1)
                        <div class="sbk-gallery-thumbs">
                            @foreach($images as $index => $image)
                                <button type="button" class="{{ $index === 0 ? 'is-active' : '' }}" data-image="{{ asset(uploadsDir('front') . $image) }}">
                                    <img src="{{ asset(uploadsDir('front') . $image) }}" alt="{{ $product->title }} image {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="sbk-section">
                    <h2>Vehicle Specifications</h2>
                    <div class="sbk-section-body">
                        <div class="sbk-spec-grid">
                            @foreach($specs as $label => $value)
                                @if($value)
                                    <div class="sbk-spec-card"><span>{{ $label }}</span><b>{{ $value }}</b></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="sbk-section sbk-description-section">
                    <h2>Description</h2>
                    <div class="sbk-section-body sbk-description">
                        <div class="sbk-description-copy">
                            {!! $product->description ?: e($product->short_description ?: 'Contact SBK for full inspection notes, export documents, and shipment details for this vehicle.') !!}
                        </div>
                    </div>
                </div>
            </div>

            <aside class="sbk-buybox">
                <div class="sbk-buybox-head">
                    <span>FOB Price</span>
                    <strong>${{ number_format($product->price, 2) }}</strong>
                    <small>Final invoice can include freight, inspection and destination charges.</small>
                </div>
                <div class="sbk-buybox-body">
                    <div class="sbk-buy-actions">
                        <form action="{{ route('cart.store', $product) }}" method="POST">
                            @csrf
                            <button type="submit">{{ in_array($product->id, $cartProductIds) ? 'Added to Cart' : 'Add to Cart' }}</button>
                        </form>
                        <form action="{{ route('wishlist.store', $product) }}" method="POST">
                            @csrf
                            <button type="submit" style="background:#111">{{ in_array($product->id, $wishlistProductIds) ? 'Wishlisted' : 'Wishlist' }}</button>
                        </form>
                        <a class="wide sbk-checkout-now" href="{{ route('checkout.index') }}">Checkout</a>
                        <a class="wide" href="{{ route('contact-us') }}">Ask About This Vehicle</a>
                    </div>
                    <div class="sbk-mini-specs">
                        <div><span>Brand</span><b>{{ optional($product->brand)->name }}</b></div>
                        <div><span>Model</span><b>{{ optional($product->vehicleModel)->name }}</b></div>
                        <div><span>Year</span><b>{{ $product->year ?: 'N/A' }}</b></div>
                        <div><span>Mileage</span><b>{{ $product->mileage_label }}</b></div>
                        <div><span>Fuel</span><b>{{ $product->fuel_type ?: 'N/A' }}</b></div>
                    </div>
                    <div class="sbk-buy-note">SBK will confirm availability, export documents and shipping route before payment.</div>
                </div>
            </aside>
        </div>
    </div>

    @if($relatedProducts->count())
        <div class="container sbk-related">
            <h2>Related Vehicles</h2>
            <div class="sbk-related-grid">
                @foreach($relatedProducts as $related)
                    <article class="sbk-related-card">
                        <img src="{{ $related->primary_image_url }}" alt="{{ $related->title }}">
                        <div>
                            <h3>{{ $related->title }}</h3>
                            <p>${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('product.show', $related) }}">View Details</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const main = document.getElementById('sbkMainProductImage');
    document.querySelectorAll('.sbk-gallery-thumbs button').forEach(function (button) {
        button.addEventListener('click', function () {
            main.src = button.dataset.image;
            document.querySelectorAll('.sbk-gallery-thumbs button').forEach(item => item.classList.remove('is-active'));
            button.classList.add('is-active');
        });
    });
});
</script>
@endsection
