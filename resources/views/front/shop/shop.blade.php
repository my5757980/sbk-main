@extends('front.layouts.app')

@php
$assetBase = asset('assets/front');
@endphp

@section('content')
<style>
.sbk-shop{--red:#d71920;--red2:#a80c13;--ink:#101114;--muted:#696b70;--line:#e8e9ec;--soft:#f6f7f8;background:#fff;color:var(--ink);padding-bottom:100px}.sbk-shop *{box-sizing:border-box}.sbk-shop .container{width:min(1280px,calc(100% - 56px));max-width:none!important;margin:auto}
.sbk-shop-hero{position:relative;min-height:360px;display:flex;align-items:center;overflow:hidden;background:linear-gradient(90deg,rgba(7,8,10,.90),rgba(7,8,10,.55),rgba(7,8,10,.28)),url("{{ $assetBase }}/img/hero-hilux.webp") center/cover no-repeat;color:#fff}.sbk-shop-hero:after{content:"SBK";position:absolute;right:-30px;bottom:-70px;font:900 260px/1 Arial,sans-serif;letter-spacing:-22px;color:rgba(255,255,255,.055)}.sbk-shop-hero__content{position:relative;z-index:2;max-width:720px}.sbk-shop-kicker{display:flex;align-items:center;gap:12px;color:#ff3b43;font-size:12px;font-weight:900;letter-spacing:2.5px;text-transform:uppercase}.sbk-shop-kicker:before{content:"";width:44px;height:3px;background:var(--red)}.sbk-shop-hero h1{font-size:clamp(48px,6vw,78px);line-height:.98;margin:16px 0 18px;letter-spacing:-3px}.sbk-shop-hero p{max-width:610px;margin:0;color:rgba(255,255,255,.82);font-size:16px;line-height:1.75}.sbk-shop-breadcrumb{margin-top:28px;font-size:12px;color:rgba(255,255,255,.7)}.sbk-shop-breadcrumb b{color:#fff}.sbk-shop-body{padding-top:62px}.sbk-shop-alert{margin-bottom:18px;padding:14px 16px;border-radius:8px;font-size:14px}.sbk-shop-alert--success{background:#ecf8ef;color:#11652f;border:1px solid #bfe8cb}.sbk-shop-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-shop-toolbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:28px;padding-bottom:22px;border-bottom:1px solid var(--line)}.sbk-shop-toolbar h2{margin:0;font-size:30px;letter-spacing:-.7px}.sbk-shop-toolbar p{margin:6px 0 0;color:var(--muted);font-size:13px}.sbk-sort{height:46px;padding:0 42px 0 16px;border:1px solid var(--line);border-radius:10px;background:#fff;color:#333;outline:none}.sbk-shop-layout{display:grid;grid-template-columns:300px minmax(0,1fr);gap:36px;align-items:start}
.sbk-filter{position:sticky;top:20px;border:1px solid var(--line);border-radius:20px;background:#fff;box-shadow:0 18px 45px rgba(16,17,20,.07);overflow:hidden}.sbk-filter__head{padding:25px 25px 21px;background:linear-gradient(135deg,#111216,#24262b);color:#fff}.sbk-filter__head h3{margin:0;font-size:22px}.sbk-filter__head span{display:block;margin-top:5px;color:rgba(255,255,255,.55);font-size:11px}.sbk-filter-group{padding:23px 24px;border-bottom:1px solid var(--line)}.sbk-filter-group:last-child{border-bottom:0}.sbk-filter-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;font-size:14px;font-weight:900}.sbk-filter-title:after{content:"-";color:var(--red);font-size:18px}.sbk-filter select,.sbk-filter input[type="number"]{width:100%;height:44px;padding:0 12px;border:1px solid #dddfe3;border-radius:9px;background:#fafafa;outline:none}.sbk-filter select:focus,.sbk-filter input:focus{border-color:var(--red);background:#fff}.sbk-checks{display:grid;gap:11px;max-height:210px;overflow:auto;padding-right:5px}.sbk-check{display:flex;align-items:center;gap:10px;color:#555;font-size:13px;cursor:pointer}.sbk-check input{accent-color:var(--red);width:16px;height:16px}.sbk-price-inputs{display:grid;grid-template-columns:1fr 1fr;gap:10px}.sbk-price-range{width:100%;margin:17px 0 5px;accent-color:var(--red)}.sbk-filter-actions{display:grid;grid-template-columns:1fr auto;gap:9px;padding:22px 24px}.sbk-apply,.sbk-reset{height:46px;border:0;border-radius:9px;font-weight:800;cursor:pointer}.sbk-apply{background:linear-gradient(135deg,var(--red),var(--red2));color:#fff;box-shadow:0 10px 22px rgba(215,25,32,.2)}.sbk-reset{padding:0 15px;background:#f1f2f4;color:#444}
.sbk-cars{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}.sbk-car{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:18px;background:#fff;box-shadow:0 12px 32px rgba(16,17,20,.06);transition:.32s}.sbk-car:hover{transform:translateY(-7px);box-shadow:0 24px 55px rgba(16,17,20,.12)}.sbk-car__media{position:relative;height:225px;overflow:hidden;background:linear-gradient(145deg,#f8f8f9,#eceef0)}.sbk-car__media img{width:100%;height:100%;object-fit:cover;transition:transform .45s}.sbk-car:hover .sbk-car__media img{transform:scale(1.045)}.sbk-car__tag{position:absolute;left:14px;top:14px;padding:7px 10px;border-radius:7px;background:var(--red);color:#fff;font-size:10px;font-weight:900;letter-spacing:.7px}.sbk-car__fav{position:absolute;right:14px;top:14px;width:38px;height:38px;border:0;border-radius:50%;background:rgba(255,255,255,.94);color:var(--red);font-size:18px;cursor:pointer}.sbk-car__body{padding:20px}.sbk-car__brand{color:var(--red);font-size:10px;font-weight:900;letter-spacing:1.4px;text-transform:uppercase}.sbk-car h3{margin:7px 0 13px;font-size:20px;line-height:1.25}.sbk-car__specs{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;padding:13px 0;border-top:1px solid #eee;border-bottom:1px solid #eee;color:#666;font-size:11px}.sbk-car__specs span{text-align:center}.sbk-car__foot{display:flex;align-items:center;justify-content:space-between;gap:10px;padding-top:16px}.sbk-price small{display:block;color:#888;font-size:9px;text-transform:uppercase;letter-spacing:.8px}.sbk-price strong{font-size:20px}.sbk-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}.sbk-action-btn{display:inline-flex;align-items:center;justify-content:center;min-height:36px;padding:0 12px;border:0;border-radius:8px;background:#111216;color:#fff;text-decoration:none;font-size:11px;font-weight:800;cursor:pointer}.sbk-action-btn--red{background:var(--red)}.sbk-action-btn:disabled{background:#999;cursor:not-allowed}.sbk-no-results{grid-column:1/-1;padding:60px 20px;text-align:center;border:1px dashed #d8d9dc;border-radius:18px;color:#777}.sbk-pagination{display:flex;justify-content:center;gap:8px;flex-wrap:wrap;margin-top:42px}.sbk-pagination a,.sbk-pagination span{min-width:42px;height:42px;padding:0 12px;display:grid;place-items:center;border:1px solid var(--line);border-radius:9px;color:#333;text-decoration:none;font-weight:700}.sbk-pagination .active,.sbk-pagination a:hover{background:var(--red);border-color:var(--red);color:#fff}.sbk-pagination .disabled{color:#aaa;background:#f6f7f8}
@media(max-width:1100px){.sbk-shop-layout{grid-template-columns:270px 1fr}.sbk-cars{grid-template-columns:repeat(2,1fr)}}@media(max-width:800px){.sbk-shop .container{width:calc(100% - 32px)}.sbk-shop-hero{min-height:320px}.sbk-shop-layout{grid-template-columns:1fr}.sbk-filter{position:relative;top:auto}.sbk-cars{grid-template-columns:repeat(2,1fr)}}@media(max-width:560px){.sbk-shop .container{width:calc(100% - 24px)}.sbk-shop-hero h1{font-size:48px}.sbk-shop-toolbar{align-items:flex-start;flex-direction:column}.sbk-sort{width:100%}.sbk-cars{grid-template-columns:1fr}.sbk-car__media{height:245px}.sbk-car__foot{align-items:flex-start;flex-direction:column}.sbk-actions{width:100%;justify-content:stretch}.sbk-actions form,.sbk-action-btn{width:100%}}
</style>

<section class="sbk-shop">
    <div class="sbk-shop-hero">
        <div class="container">
            <div class="sbk-shop-hero__content">
                <span class="sbk-shop-kicker">Global Vehicle Inventory</span>
                <h1>Find Your<br>Next Car.</h1>
                <p>Browse quality vehicles selected for international buyers. Filter by brand, model and price to quickly find the right vehicle for you.</p>
                <div class="sbk-shop-breadcrumb"><b>Home</b> &nbsp;/&nbsp; Shop</div>
            </div>
        </div>
    </div>

    <div class="container sbk-shop-body">
        @if(session('success'))
            <div class="sbk-shop-alert sbk-shop-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="sbk-shop-alert sbk-shop-alert--error">{{ session('error') }}</div>
        @endif

        <div class="sbk-shop-toolbar">
            <div>
                <h2>Available Vehicles</h2>
                <p><span id="sbkResultCount">{{ $products->total() }}</span> vehicles found</p>
            </div>
            <select class="sbk-sort" id="sbkSort" name="sort" form="sbkFilterForm" onchange="document.getElementById('sbkFilterForm').submit()">
                <option value="default" @selected($sort === 'default')>Sort by: Featured</option>
                <option value="price-low" @selected($sort === 'price-low')>Price: Low to High</option>
                <option value="price-high" @selected($sort === 'price-high')>Price: High to Low</option>
                <option value="year-new" @selected($sort === 'year-new')>Year: Newest</option>
            </select>
        </div>

        <div class="sbk-shop-layout">
            <form class="sbk-filter" id="sbkFilterForm" method="GET" action="{{ route('shop') }}">
                @include('front.partials.shop-advanced-parameters')
                @if($search !== '')
                    <input type="hidden" name="search" value="{{ $search }}">
                @endif
                <div class="sbk-filter__head"><h3>Filter Vehicles</h3><span>Refine your search</span></div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Price Range</div>
                    <div class="sbk-price-inputs">
                        <input id="sbkMinPrice" name="min_price" type="number" placeholder="Min $" min="0" value="{{ $minPrice }}">
                        <input id="sbkMaxPrice" name="max_price" type="number" placeholder="Max $" min="0" value="{{ $maxPrice }}">
                    </div>
                    <input class="sbk-price-range" id="sbkPriceRange" type="range" min="0" max="{{ $maxAvailablePrice }}" step="500" value="{{ $maxPrice ?: $maxAvailablePrice }}">
                    <small style="color:#888">Up to $<span id="sbkRangeValue">{{ number_format($maxPrice ?: $maxAvailablePrice) }}</span></small>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Brand</div>
                    <div class="sbk-checks">
                        @foreach($brands as $brand)
                            <label class="sbk-check"><input class="sbkBrand" name="brands[]" type="checkbox" value="{{ $brand->name }}" @checked(in_array($brand->name, $selectedBrands, true))> <span>{{ $brand->name }}</span></label>
                        @endforeach
                    </div>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">By Type</div>
                    <div class="sbk-checks">
                        @foreach($types as $type)
                            <label class="sbk-check"><input name="types[]" type="checkbox" value="{{ $type }}" @checked(in_array($type, $selectedTypes, true))> <span>{{ $type }}</span></label>
                        @endforeach
                    </div>
                </div>
                @if($grades->count())
                    <div class="sbk-filter-group">
                        <div class="sbk-filter-title">Grade</div>
                        <div class="sbk-checks">
                            @foreach($grades as $grade)
                                <label class="sbk-check"><input name="grades[]" type="checkbox" value="{{ $grade }}" @checked(in_array($grade, $selectedGrades, true))> <span>{{ $grade }}</span></label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Model</div>
                    <select id="sbkModel" name="model">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model->name }}" data-brand="{{ optional($model->brand)->name }}" @selected($selectedModel === $model->name)>{{ optional($model->brand)->name }} - {{ $model->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Year</div>
                    <select id="sbkYear" name="year">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected((string) $selectedYear === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Mileage</div>
                    <select id="sbkMileage" name="max_mileage">
                        <option value="">Any Mileage</option>
                        @for($mileage = 50000; $mileage <= 250000; $mileage += 50000)
                            <option value="{{ $mileage }}" @selected((string) $selectedMileage === (string) $mileage)>Under {{ number_format($mileage) }} km</option>
                        @endfor
                    </select>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Transmission</div>
                    <select id="sbkTransmission" name="transmission">
                        <option value="">Any Transmission</option>
                        <option value="Automatic" @selected($selectedTransmission === 'Automatic')>Automatic Transmission</option>
                        <option value="Manual" @selected($selectedTransmission === 'Manual')>Manual Transmission</option>
                    </select>
                </div>
                <div class="sbk-filter-group">
                    <div class="sbk-filter-title">Steering</div>
                    <select id="sbkSteering" name="steering" aria-label="Steering">
                        <option value="">Any Steering</option>
                        <option value="LHD" @selected($selectedSteering === 'LHD')>Left Hand Drive</option>
                        <option value="RHD" @selected($selectedSteering === 'RHD')>Right Hand Drive</option>
                    </select>
                </div>
                <div class="sbk-filter-actions"><button class="sbk-apply" id="sbkApply" type="submit">Apply Filters</button><a class="sbk-reset" id="sbkReset" href="{{ route('shop') }}" style="display:grid;place-items:center;text-decoration:none">Reset</a></div>
            </form>

            <main>
                <div class="sbk-cars" id="sbkCars">
                    @forelse($products as $product)
                        <article class="sbk-car" data-card-url="{{ route('product.show', $product) }}" data-brand="{{ optional($product->brand)->name }}" data-model="{{ optional($product->vehicleModel)->name }}" data-year="{{ $product->year }}" data-price="{{ (float) $product->price }}">
                            <div class="sbk-car__media">
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->title }}">
                                @if($product->tag)<span class="sbk-car__tag">{{ $product->tag }}</span>@endif
                                <form action="{{ route('wishlist.store', $product) }}" method="POST">
                                    @csrf
                                    <button class="sbk-car__fav" type="submit" title="Add to wishlist">{!! in_array($product->id, $wishlistProductIds) ? '&hearts;' : '&#9825;' !!}</button>
                                </form>
                            </div>
                            <div class="sbk-car__body">
                                <span class="sbk-car__brand">{{ optional($product->brand)->name }}</span>
                                <h3><a href="{{ route('product.show', $product) }}" style="color:inherit;text-decoration:none">{{ $product->title }}</a></h3>
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
                                            <button class="sbk-action-btn sbk-action-btn--red" type="submit">{{ in_array($product->id, $cartProductIds) ? 'Added' : 'Add Cart' }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="sbk-no-results" id="sbkNoResults"><strong>No vehicles found.</strong><br>Try changing your filters.</div>
                    @endforelse
                </div>
                @if($products->hasPages())
                    <nav class="sbk-pagination" aria-label="Vehicle pagination">
                        @if($products->onFirstPage())
                            <span class="disabled">Prev</span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}">Prev</a>
                        @endif

                        @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            @if($page === $products->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}">Next</a>
                        @else
                            <span class="disabled">Next</span>
                        @endif
                    </nav>
                @endif
            </main>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded',function(){
 const range=document.getElementById('sbkPriceRange'), max=document.getElementById('sbkMaxPrice'), rv=document.getElementById('sbkRangeValue');
 if(range&&max&&rv){range.addEventListener('input',()=>{max.value=range.value;rv.textContent=Number(range.value).toLocaleString()})}
 const brandInputs=[...document.querySelectorAll('.sbkBrand')], model=document.getElementById('sbkModel');
 function filterModelOptions(){
  if(!model)return;
  const selectedBrands=brandInputs.filter(input=>input.checked).map(input=>input.value);
  [...model.options].forEach(option=>{
   if(!option.value){option.hidden=false;return}
   option.hidden=selectedBrands.length>0&&!selectedBrands.includes(option.dataset.brand);
  });
  if(model.selectedOptions[0]&&model.selectedOptions[0].hidden){model.value=''}
 }
 brandInputs.forEach(input=>input.addEventListener('change',filterModelOptions));
 filterModelOptions();
});
</script>
@endsection
