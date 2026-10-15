@extends('front.layouts.app')
@section('content')
@php
    $assetBase = asset('assets/front');
    $bestSellerProducts = $bestSellerProducts ?? collect();
    $upcomingCollectionProducts = $upcomingCollectionProducts ?? collect();
    $newArrivalProducts = $newArrivalProducts ?? collect();
    $commercialProducts = $commercialProducts ?? collect();
    $homeBlogs = $homeBlogs ?? collect();
    $cartProductIds = $cartProductIds ?? [];
    $wishlistProductIds = $wishlistProductIds ?? [];
    $homeFilterBrands = $homeFilterBrands ?? collect();
    $homeFilterModels = $homeFilterModels ?? collect();
    $homeFilterYears = $homeFilterYears ?? collect();
    $brands = [
        ['Toyota', 'img/brand-toyota.webp'],
        ['BMW', 'img/brand-bmw.webp'],
        ['Mercedes-Benz', 'img/brand-mercedes.webp'],
        ['Hino', 'img/brand-hino.webp'],
        ['Mazda', 'img/brand-mazda.webp'],
        ['Volkswagen', 'img/brand-vw.webp'],
    ];
    $hero = [
        ['2024 HYBRID', 'TOYOTA PRIUS PRIME', 'Eco-Friendly Technology with Exceptional Efficiency', 'CVT', 'Hybrid', '8,500 Km', 'img/home-hero-prius-prime-2024.jpg'],
        ['2026 NEW ARRIVAL', 'TOYOTA HILUX TRAVO', 'Overland Plus Edition - Premium Off-Road Performance', 'Automatic', 'Diesel 4WD', '0 Km', 'img/home-hero-hilux-travo.webp'],
        ['2025 PREMIUM', 'HYUNDAI SANTA FE', 'Luxury SUV with Advanced Safety Features', 'Automatic', 'Gasoline', '0 Km', 'img/home-hero-santafe-2025.jpg'],
    ];
    $cars = [
        ['TOYOTA MARK X Black 2016', '2016 Toyota Mark X 250G F Package in Black with GRX130 chassis and automatic transmission.', '$8,200.00', 'img/home-cutout-markx-black-2016.png', ''],
        ['TOYOTA MARK X White 2016', '2016 Toyota Mark X 250G Package in White with 2500 cc engine and FAT transmission.', '$7,800.00', 'img/home-cutout-markx-white-2016.png', ''],
        ['MERCEDES BENZ GLS Black', 'Luxury SUV with leather seats, panoramic sunroof, navigation, 360 camera, and roof rails.', '$21,550.00', 'img/home-cutout-mercedes-gls-black-2016.png', '5% OFF'],
        ['MERCEDES-BENZ GLS Black 2016', 'Premium black Mercedes-Benz GLS with comfort features and powerful SUV styling.', '$21,880.00', 'img/home-cutout-mercedes-gls-black-2016-2.png', '5% OFF'],
        ['Honda Fit White - 2015', 'Honda Fit 2015 with practical hatchback appeal, economy, and everyday comfort.', '$4,700.00', 'img/home-cutout-honda-fit-white-2015.png', 'Reserved'],
        ['TOYOTA MARK X 2015', 'Pearl white Toyota Mark X with navigation, alloy wheels, cameras, and parking sensors.', '$6,200.00', 'img/home-cutout-markx-white-2016.png', ''],
        ['TOYOTA HIACE VAN - 2016', 'Reliable white Toyota Hiace DX with automatic gearbox and 2.0L gasoline engine.', '$9,000.00', 'img/home-cutout-hiace-van-2016.png', ''],
        ['TOYOTA ALLION 2015 Silver', 'Dependable 4WD automatic sedan with ABS, airbags, fog lights, and clean finish.', '$7,900.00', 'img/home-cutout-allion-silver-2015.png', '5% OFF'],
    ];
    $upcoming = [
        ['Honda Fit White - 2015', '$4,700.00', 'img/home-cutout-honda-fit-white-2015.png'],
        ['TOYOTA HIACE VAN - 2016', '$9,000.00', 'img/home-cutout-hiace-van-2016.png'],
        ['Toyota Crown 2.5 Z Silver', '$45,716.00', 'img/home-cutout-crown-silver.png'],
        ['Toyota Crown Sport Z Black', '$41,930.00', 'img/home-cutout-crown-sport-black.png'],
    ];
    $bodyTypes = [
        ['Sedan', 'img/home-cutout-markx-white-2016.png', ['types' => ['Sedan']]], ['SUV', 'img/home-cutout-mercedes-gls-black-2016.png', ['types' => ['SUV']]], ['Sports', 'img/body-type-sports-cutout.png', ['types' => ['Coupe']]], ['Convertible', 'img/body-type-convertible-cutout.png', ['types' => ['Convertible / Roadster']]],
        ['Compact', 'img/home-cutout-honda-fit-white-2015.png', ['types' => ['Hatchback']]], ['Pick Up', 'img/home-cutout-hilux-travo.png', ['types' => ['Truck / Double Cab']]], ['Crossover', 'img/home-cutout-santafe-2025.png', ['types' => ['SUV']]], ['Electric', 'img/home-cutout-prius-prime-2024.png', ['search' => 'Electric']],
    ];
    $faqs = [
        'Where is your showroom located?',
        'How can I know if I can import a vehicle into my country?',
        'Where do you source your vehicles from?',
        'How do I purchase a vehicle through SBK?',
        'What deposit is required to start the search?',
        'Who handles export and shipping?',
        'How long does shipping take?',
        'What is the payment structure?',
    ];

    if (isset($featuredProducts) && $featuredProducts->count()) {
        $cars = $featuredProducts->map(function ($product) {
            return [
                $product->title,
                $product->short_description ?: trim(($product->year ? $product->year . ' ' : '') . optional($product->brand)->name . ' ' . optional($product->vehicleModel)->name),
                '$' . number_format($product->price, 2),
                $product->primary_image_url,
                $product->tag ?: ($product->is_featured ? 'Featured' : ''),
            ];
        })->values()->all();

        $upcoming = array_slice(array_map(function ($car) {
            return [$car[0], $car[2], $car[3]];
        }, $cars), 0, 4);
    }
@endphp



<style>
.hero,.hero__slide{min-height:560px}.hero__content{min-height:510px}.hero h1{font-size:58px}.hero__tabs button{padding-top:13px;padding-bottom:13px}.sbk-home-search{position:relative;z-index:8;margin:-44px 0 36px}.sbk-home-search__panel{position:relative;overflow:hidden;border:1px solid rgba(17,18,22,.08);border-radius:14px;background:#fff;box-shadow:0 24px 70px rgba(17,18,22,.18)}.sbk-home-search__panel:before{content:"";position:absolute;left:0;top:0;width:100%;height:4px;background:linear-gradient(90deg,#d71920,#111216)}.sbk-home-search__head{display:flex;justify-content:space-between;gap:18px;align-items:flex-end;padding:24px 26px 16px;border-bottom:1px solid #eef0f3}.sbk-home-search__head span{display:block;color:#d71920;font-size:11px;font-weight:900;letter-spacing:1.8px;text-transform:uppercase}.sbk-home-search__head h2{margin:5px 0 0;font-size:26px;line-height:1.05}.sbk-home-search__head p{max-width:410px;margin:0;color:#6b717b;font-size:13px;line-height:1.55}.sbk-home-filter{display:grid;grid-template-columns:repeat(6,minmax(0,1fr)) auto;gap:13px;align-items:end;padding:22px 26px 26px}.sbk-home-field{display:grid;gap:8px}.sbk-home-field label{color:#111216;font-size:11px;font-weight:900;letter-spacing:.8px;text-transform:uppercase}.sbk-home-control{position:relative}.sbk-home-control:after{content:"";position:absolute;right:14px;top:50%;width:8px;height:8px;border-right:2px solid #d71920;border-bottom:2px solid #d71920;transform:translateY(-65%) rotate(45deg);pointer-events:none}.sbk-home-control select{width:100%;height:50px;appearance:none;border:1px solid #dfe2e7;border-radius:8px;background:#fafbfc;color:#222;padding:0 38px 0 14px;font:inherit;font-size:13px;outline:none}.sbk-home-control select:focus{border-color:#d71920;background:#fff;box-shadow:0 0 0 4px rgba(215,25,32,.08)}.sbk-home-submit{height:50px;min-width:148px;border:0;border-radius:8px;background:linear-gradient(135deg,#d71920,#a90f15);color:#fff;font-size:12px;font-weight:900;letter-spacing:.5px;text-transform:uppercase;box-shadow:0 14px 30px rgba(215,25,32,.24);cursor:pointer}.sbk-home-submit:hover{background:linear-gradient(135deg,#111216,#d71920)}.sbk-home-search__quick{display:flex;gap:10px;flex-wrap:wrap;padding:0 26px 24px}.sbk-home-search__quick a{display:inline-flex;align-items:center;min-height:30px;padding:0 12px;border-radius:999px;background:#f4f5f7;color:#444;font-size:11px;font-weight:800}.sbk-home-search__quick a:hover{background:#111216;color:#fff}@media(max-width:1180px){.sbk-home-filter{grid-template-columns:repeat(3,minmax(0,1fr))}.sbk-home-submit{width:100%}}@media(max-width:980px){.sbk-home-filter{grid-template-columns:repeat(2,minmax(0,1fr))}.sbk-home-submit{width:100%}.hero,.hero__slide{min-height:530px}.hero__content{min-height:490px}}@media(max-width:620px){.hero,.hero__slide{min-height:500px!important}.hero__content{min-height:460px!important}.sbk-home-search{margin:-24px 0 28px}.sbk-home-search__head{display:block;padding:20px}.sbk-home-search__head p{margin-top:10px}.sbk-home-filter{grid-template-columns:1fr;padding:20px}.sbk-home-search__quick{padding:0 20px 20px}.hero h1{font-size:44px!important}}
img[src*="/img/home-cutout-"]{object-fit:contain!important;filter:drop-shadow(0 16px 16px rgba(17,18,22,.18))}
</style>

 
<style>
/* Homepage banner: full-width photography with a soft readability overlay. */
.hero .hero__slide {
    background-color: #0b0e12;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center 62%;
}
.hero .hero__shade {
    background: linear-gradient(90deg, rgba(5,9,14,.76) 0%, rgba(5,9,14,.48) 35%, rgba(5,9,14,.12) 72%, rgba(5,9,14,.08) 100%);
}
@media (min-width: 981px) {
    .hero, .hero .hero__slide, .hero .hero__content {
        min-height: clamp(560px, 48vw, 740px);
    }
}
@media (max-width: 620px) {
    .hero .hero__slide {
        background-position: 58% center;
    }
    .hero .hero__shade {
        background: linear-gradient(180deg, rgba(5,9,14,.18), rgba(5,9,14,.48) 35%, rgba(5,9,14,.86) 100%);
    }
}
</style>
        <section class="hero" data-slider data-autoplay="true">
            @foreach($hero as $index => $slide)
                <article class="hero__slide {{ $index === 0 ? 'is-active' : '' }}" data-slide style="background-image: url('{{ $assetBase }}/{{ $slide[6] }}')">
                    <div class="hero__shade"></div>
                    <div class="container hero__content">
                        <span class="badge">Featured</span>
                        <p>{{ $slide[0] }}</p>
                        <h1>{{ $slide[1] }}</h1>
                        <h2>{{ $slide[2] }}</h2>
                        <div class="hero__specs">
                            <span>{{ $slide[3] }}</span>
                            <span>{{ $slide[4] }}</span>
                            <span>{{ $slide[5] }}</span>
                        </div>
                        <div class="hero__buttons">
                            <a href="{{ route('shop') }}">Buy Now</a>
                            <a href="{{ route('shop') }}">View Details</a>
                        </div>
                    </div>
                </article>
            @endforeach
            <div class="hero__stamp" aria-label="SBK Global Auto Trading Ready To Ship">
                <svg class="hero__stamp-ring" viewBox="0 0 200 200" aria-hidden="true">
                    <defs>
                        <path id="stampCircle" d="M100,100 m-78,0 a78,78 0 1,1 156,0 a78,78 0 1,1 -156,0"></path>
                    </defs>
                    <text>
                        <textPath href="#stampCircle" startOffset="0%">SBK GLOBAL AUTO TRADING * SBK GLOBAL AUTO TRADING *</textPath>
                    </text>
                </svg>
                <span class="hero__stamp-core">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h11v8H3V7Z"/><path d="M14 10h3l3 3v2h-6v-5Z"/><path d="M6 17h.01M17 17h.01"/><path d="M7 10v3M10 10v3"/></svg>
                    <strong>READY</strong>
                    <small>TO SHIP</small>
                </span>
            </div>
            <button class="hero__arrow hero__arrow--prev" type="button" data-slider-prev aria-label="Previous slide">‹</button>
            <button class="hero__arrow hero__arrow--next" type="button" data-slider-next aria-label="Next slide">›</button>
            <div class="hero__tabs">
                @foreach($hero as $index => $slide)
                    <button class="{{ $index === 0 ? 'is-active' : '' }}" type="button" data-slider-dot>{{ $slide[1] }}</button>
                @endforeach
            </div>
        </section>

        <section class="sbk-home-search" aria-label="Find a vehicle">
            <div class="container">
                <div class="sbk-home-search__panel">
                    <div class="sbk-home-search__head">
                        <div>
                            <span>Find Your Vehicle</span>
                            <h2>Search SBK Stock Faster</h2>
                        </div>
                        <p>Select brand, model, price, and year. We will take you to the shop page with matching vehicles.</p>
                    </div>
                    <form class="sbk-home-filter" id="sbkHomeFilter" method="GET" action="{{ route('shop') }}">
                        <input type="hidden" name="min_price" id="homeMinPrice">
                        <input type="hidden" name="max_price" id="homeMaxPrice">
                        <div class="sbk-home-field">
                            <label for="homeBrand">Brand</label>
                            <div class="sbk-home-control">
                                <select id="homeBrand" name="brands[]">
                                    <option value="">All Brands</option>
                                    @foreach($homeFilterBrands as $brand)
                                        <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="sbk-home-field">
                            <label for="homeModel">Model</label>
                            <div class="sbk-home-control">
                                <select id="homeModel" name="model">
                                    <option value="">All Models</option>
                                    @foreach($homeFilterModels as $model)
                                        <option value="{{ $model->name }}" data-brand="{{ optional($model->brand)->name }}">{{ optional($model->brand)->name }} - {{ $model->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="sbk-home-field">
                            <label for="homePrice">Price</label>
                            <div class="sbk-home-control">
                                <select id="homePrice" name="price_range">
                                    <option value="">Any Price</option>
                                    <option value="0-5000">Under $5,000</option>
                                    <option value="5000-10000">$5,000 - $10,000</option>
                                    <option value="10000-20000">$10,000 - $20,000</option>
                                    <option value="20000-40000">$20,000 - $40,000</option>
                                    <option value="40000-">$40,000+</option>
                                </select>
                            </div>
                        </div>
                        <div class="sbk-home-field">
                            <label for="homeYear">Year</label>
                            <div class="sbk-home-control">
                                <select id="homeYear" name="year">
                                    <option value="">All Years</option>
                                    @foreach($homeFilterYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="sbk-home-field">
                            <label for="homeMileage">Mileage</label>
                            <div class="sbk-home-control">
                                <select id="homeMileage" name="max_mileage">
                                    <option value="">Any Mileage</option>
                                    @for($mileage = 50000; $mileage <= 250000; $mileage += 50000)
                                        <option value="{{ $mileage }}">Under {{ number_format($mileage) }} km</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="sbk-home-field">
                            <label for="homeTransmission">Transmission</label>
                            <div class="sbk-home-control">
                                <select id="homeTransmission" name="transmission">
                                    <option value="">Any Transmission</option>
                                    <option value="Automatic">Automatic Transmission</option>
                                    <option value="Manual">Manual Transmission</option>
                                </select>
                            </div>
                        </div>
                        <button class="sbk-home-submit" type="submit">Search Cars</button>
                        @include('front.partials.home-advanced-search')
                    </form>
                    <div class="sbk-home-search__quick">
                        <a href="{{ route('shop') }}">View all stock</a>
                        <a href="{{ route('shop', ['sort' => 'price-low']) }}">Best price first</a>
                        <a href="{{ route('shop', ['sort' => 'year-new']) }}">Newest year</a>
                    </div>
                </div>
            </div>
        </section>

        @include('front.partials.home-steering')

        <section class="brand-intro reveal-on-scroll">
            <div class="container center">
                <h2>Shop by World Known Brand</h2>
                <p>SBK offers a wide range of leading automotive brands, combining reliability, innovation, and style for every driver.</p>
            </div>
        </section>

        <section class="brand-strip reveal-on-scroll">
            <div class="brand-marquee" aria-label="World known vehicle brands">
                <div class="brand-row">
                    @foreach($brands as $brand)
                        <div><a href="{{ route('shop', ['brands' => [$brand[0]]]) }}" aria-label="Shop {{ $brand[0] }}" style="display:grid;place-items:center;width:100%;height:100%"><img src="{{ $assetBase }}/{{ $brand[1] }}" alt="{{ $brand[0] }}"></a></div>
                    @endforeach
                    @foreach($brands as $brand)
                        <div><a href="{{ route('shop', ['brands' => [$brand[0]]]) }}" aria-label="Shop {{ $brand[0] }}" style="display:grid;place-items:center;width:100%;height:100%"><img src="{{ $assetBase }}/{{ $brand[1] }}" alt=""></a></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="brand-stats reveal-on-scroll">
            <div class="container center">
                <div class="stats">
                    <div><strong>15</strong><span>Years in<br>Business</span></div>
                    <div><strong>1070+</strong><span>Unit Sold</span></div>
                    <div><strong>3,000+</strong><span>International Clientele</span></div>
                    <div><strong>30+</strong><span>Countries Covered</span></div>
                </div>
                <h3>Global Reach With Proven Expertise</h3>
                <p>SBK Auto Trading has gained the trust of happy clients by building a reputation for dependability, honesty, and quality.</p>
                <a class="btn" href="{{ route('about-us') }}">Read More</a>
            </div>
        </section>

        <section class="section section--tint">
            <div class="container">
                <h2>This Week's Best Seller</h2>
                <p class="lead">View the most popular vehicles buyers are choosing this week.</p>
                <div class="rail-slider" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous best seller">‹</button>
                    <div class="product-grid rail-track">
                        @if($bestSellerProducts->count())
                            @foreach($bestSellerProducts as $product)
                                @include('front.partials.home-product-card', ['product' => $product])
                            @endforeach
                        @else
                            @foreach($cars as $car)
                            <article class="product-card rail-item">
                                @if($car[4]) <span class="ribbon">{{ $car[4] }}</span> @endif
                                <img src="{{ \Illuminate\Support\Str::startsWith($car[3], ['http://', 'https://', '/']) ? $car[3] : $assetBase . '/' . $car[3] }}" alt="{{ $car[0] }}">
                                <div>
                                    <h3>{{ $car[0] }}</h3>
                                    <p>{{ $car[1] }}</p>
                                    <strong>{{ $car[2] }}</strong>
                                </div>
                            </article>
                            @endforeach
                        @endif
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next best seller">›</button>
                </div>
                <div class="center"><a class="btn" href="{{ route('shop') }}">View All Recent Seller</a></div>
            </div>
        </section>

        <section class="why">
            <div class="container">
                <h2>Why Choose SBK Global Auto Trading</h2>
                <div class="why-grid">
                    @foreach([
                        ['Trusted Expertise', 'SBK has 15 years of experience in global automotive trading, sourcing vehicles from verified suppliers to ensure quality, reliability, and professional service.', 'expertise'],
                        ['Extensive Vehicle Selection', 'Access a wide range of Japanese, European, and American vehicles, and trucks, offering choices for every requirement, budget, and industry need.', 'selection'],
                        ['Transparent & Reliable', 'All vehicles come with authentic auction sheets and detailed condition reports, giving clients full clarity and confidence in every purchase.', 'reliable'],
                        ['Flexible Purchase Options', 'Buy directly from our ready stock or participate in Japanese auctions, providing freedom, variety, and opportunities to secure the right vehicle.', 'purchase'],
                        ['Global Reach', 'Serving clients across 30+ countries with reliable shipping, documentation, and logistics support, ensuring smooth international transactions.', 'global'],
                        ['Customer-Focused Service', 'SBK prioritizes clear communication and after-sales assistance, guiding clients through every step for a seamless, worry-free trading experience.', 'service'],
                    ] as $item)
                        <article class="why-card" tabindex="0">
                            <div class="why-card__inner">
                                <div class="why-card__face why-card__front">
                                    <span class="why-icon why-icon--{{ $item[2] }}">
                                        @switch($item[2])
                                            @case('expertise')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 4.2-2.8 8.1-7 9-4.2-.9-7-4.8-7-9V6l7-3Z"/><path d="M9 12l2 2 4-5"/></svg>
                                                @break
                                            @case('selection')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 16h14l-1.4-4.2A3 3 0 0 0 14.8 10H9.2a3 3 0 0 0-2.8 1.8L5 16Z"/><path d="M7 16v2M17 16v2M8 13h8"/><circle cx="7.5" cy="18" r="1.5"/><circle cx="16.5" cy="18" r="1.5"/></svg>
                                                @break
                                            @case('reliable')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h8l4 4v14H7V3Z"/><path d="M15 3v5h5M9 13h6M9 17h4"/></svg>
                                                @break
                                            @case('purchase')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16l-2 10H7L4 7Z"/><path d="M4 7 3 4H1M9 20h.01M17 20h.01M9 12h6"/></svg>
                                                @break
                                            @case('global')
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><path d="M4 12h16M12 4c2 2.2 3 4.8 3 8s-1 5.8-3 8c-2-2.2-3-4.8-3-8s1-5.8 3-8Z"/></svg>
                                                @break
                                            @default
                                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 12a5 5 0 0 1 10 0v3a3 3 0 0 1-3 3h-2"/><path d="M5 12h3v5H5a2 2 0 0 1-2-2v-1a2 2 0 0 1 2-2ZM19 12h-3v5h3a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2Z"/><path d="M12 18h2"/></svg>
                                        @endswitch
                                    </span>
                                    <h3>{{ $item[0] }}</h3>
                                    <button type="button">Explore</button>
                                </div>
                                <div class="why-card__face why-card__back">
                                    <h3>{{ $item[0] }}</h3>
                                    <p>{{ $item[1] }}</p>
                                    <a href="{{ route('about-us') }}">Learn More</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section payments">
            <div class="container center">
                <h2>We are the Member of</h2>
                <div class="members">
                    <article><img src="{{ $assetBase }}/img/member-jct.webp" alt="Japanese Car Trade"><span>Japanese Car Trade</span></article>
                    <article><img src="{{ $assetBase }}/img/member-at.webp" alt="Auto Trader"><span>Aisha Trading LLC</span></article>
                    <article><img src="{{ $assetBase }}/img/member-tvc.webp" alt="TCV"><span>TCV Partner Network</span></article>
                </div>
                <h3 class="payment-title">Pick Your Payment Method</h3>
                <div class="payment-card">
                    <div>
                        <h4>Transparent Payments</h4>
                        <p>No Surprises Nothing matters more to us than providing a secure and clear payment process. You'll be informed about all costs upfront, including vehicle price, shipping, and fees, so there are no surprises, just a smooth and trusted experience from start to finish.</p>
                    </div>
                    <div>
                        <p>Smooth, Secure Payments for Your Vehicle Purchase</p>
                        <h4>Our Payment Options at <span>SBK Global Auto Trading</span></h4>
                        <img src="{{ $assetBase }}/img/pay-mastercard.webp" alt="Mastercard">
                        <img src="{{ $assetBase }}/img/pay-visa.webp" alt="Visa">
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>The Exclusive New Stock</h2>
                <p class="lead">Explore our newest uploaded vehicles.</p>
                <div class="rail-slider rail-slider--mini" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous new stock vehicle">‹</button>
                    <div class="{{ $upcomingCollectionProducts->count() ? 'product-grid' : 'mini-row' }} rail-track">
                        @if($upcomingCollectionProducts->count())
                            @foreach($upcomingCollectionProducts as $product)
                                @include('front.partials.home-product-card', ['product' => $product])
                            @endforeach
                        @else
                            @foreach($upcoming as $item)
                                <article class="rail-item"><img src="{{ \Illuminate\Support\Str::startsWith($item[2], ['http://', 'https://', '/']) ? $item[2] : $assetBase . '/' . $item[2] }}" alt="{{ $item[0] }}"><h3>{{ $item[0] }}</h3><strong>{{ $item[1] }}</strong></article>
                            @endforeach
                        @endif
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next new stock vehicle">›</button>
                </div>
                <div class="center"><a class="btn" href="{{ route('shop') }}">View All New Stock</a></div>
            </div>
        </section>

        <section class="section section--tint">
            <div class="container">
                <h2>Luxury Vehicles</h2>
                <p class="lead">Explore our luxury vehicles priced at $70,000 and above.</p>
                <div class="rail-slider" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous luxury vehicle">‹</button>
                    <div class="product-grid product-grid--four rail-track">
                        @if($newArrivalProducts->count())
                            @foreach($newArrivalProducts as $product)
                                @include('front.partials.home-product-card', ['product' => $product])
                            @endforeach
                        @else
                            <p>No luxury vehicles are currently available.</p>
                        @endif
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next luxury vehicle">›</button>
                </div>
                <div class="center"><a class="btn" href="{{ route('shop', ['min_price' => 70000]) }}">View All Luxury Vehicles</a></div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <h2>Commercial Vehicles</h2>
                <p class="lead">Explore our trucks, with the latest uploads shown first.</p>
                <div class="rail-slider" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous commercial vehicle">&#8249;</button>
                    <div class="product-grid product-grid--four rail-track">
                        @forelse($commercialProducts as $product)
                            @include('front.partials.home-product-card', ['product' => $product])
                        @empty
                            <p>No commercial vehicles are currently available.</p>
                        @endforelse
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next commercial vehicle">&#8250;</button>
                </div>
                <div class="center"><a class="btn" href="{{ route('shop', ['types' => ['Truck / Double Cab']]) }}">View All Commercial Vehicles</a></div>
            </div>
        </section>
        <section class="section steps">
            <div class="container">
                <p class="eyebrow">How It Works</p>
                <h2>Buy Your Dream Car in 3 Easy Steps</h2>
                <div class="step-grid">
                    <article><span class="step-icon step-icon--chat"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 6h14v9H9l-4 4V6Z"/><path d="M8 10h8M8 13h5"/></svg></span><h3>Talk to the Agent</h3><p>Confirm details, total cost, payment steps, and questions with our team.</p></article>
                    <article><span class="step-icon step-icon--car"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 15h16l-1.5-4.4A3 3 0 0 0 15.7 8H8.3a3 3 0 0 0-2.8 2.6L4 15Z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M8 12h8"/></svg></span><h3>Browse and Select a Vehicle</h3><p>Choose from Japanese vehicles and review photos, mileage, model year, and price.</p></article>
                    <article><span class="step-icon step-icon--ship"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h16l-2 5H6l-2-5Z"/><path d="M7 13V8h7l3 5M9 8V5h4v3M6 21c1.2-.8 2.4-.8 3.6 0 1.2.8 2.4.8 3.6 0 1.2-.8 2.4-.8 3.6 0"/></svg></span><h3>Confirm Your Shipment</h3><p>We arrange export and shipment and keep you updated until departure.</p></article>
                </div>
            </div>
        </section>

        <section class="auction">
            <div class="container auction__grid">
                <div>
                    <p class="eyebrow">Auction Buying Support</p>
                    <h2>Tell Us What You Need, We Bid for You</h2>
                    <p>Share your preferred model, budget, year, mileage, and condition. Our agent searches Japanese auctions and bids on your behalf for the right car at the right price.</p>
                    <a class="btn" href="{{ route('contact-us') }}">Request Auction Support</a>
                </div>
                <img src="{{ $assetBase }}/img/home-cutout-hilux-travo.png" alt="Auction support vehicle">
            </div>
        </section>

        <section class="section body-search">
            <div class="container center">
                <p class="eyebrow">Pick Your Perfect Match</p>
                <h2>Search by Body Type</h2>
                <div class="body-grid">
                    @foreach($bodyTypes as $type)
                        <article><a href="{{ route('shop', $type[2]) }}" style="display:block;color:inherit;text-decoration:none"><img src="{{ $assetBase }}/{{ $type[1] }}" alt="{{ $type[0] }}" @if(in_array($type[0], ['SUV', 'Compact', 'Pick Up', 'Electric'], true)) style="transform:scaleX(-1)" @endif><h3>{{ $type[0] }}</h3></a></article>
                    @endforeach
                </div>
                <a class="btn" href="{{ route('shop') }}">Check Our Shop</a>
            </div>
        </section>

        <section class="section videos">
            <div class="container">
                <h2>Customer By Client Video</h2>
                <div class="rail-slider rail-slider--video" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous client video">‹</button>
                    <div class="video-grid rail-track">
                        @foreach(['client-1.mp4','client-2.mp4','client-3.mp4','client-4.mp4'] as $video)
                            <video class="rail-item" controls muted preload="metadata"><source src="{{ $assetBase }}/video/{{ $video }}" type="video/mp4"></video>
                        @endforeach
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next client video">›</button>
                </div>
            </div>
        </section>

        <section class="testimonials">
            <div class="container testimonial-grid">
                <div class="testimonial-copy"><p class="eyebrow">Testimonials</p><h2>Trusted By 1000+ Car Lovers</h2><p>Real buyers trust SBK for clear communication, inspected vehicles, and smooth export support.</p></div>
                <article class="testimonial-card">
                    <span class="testimonial-card__quote"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 7H5v5h3v5H3V9.5C3 6.5 4.7 5 7.6 5H9v2Zm12 0h-4v5h3v5h-5V9.5c0-3 1.7-4.5 4.6-4.5H21v2Z"/></svg></span>
                    <p>I was not sure how to buy a car from Japan, but the SBK team explained each step properly. They shared the vehicle details, price, and shipping updates on time.</p>
                    <div class="testimonial-card__person"><span>DM</span><div><strong>Daniel M.</strong><small>New York, NY</small></div></div>
                </article>
            </div>
        </section>

        <section class="register">
            <div class="container register__grid">
                <div>
                    <p class="eyebrow">Ready to Import?</p>
                    <h2>Register with SBK Today for Unbeatable Prices</h2>
                    <p>Tell us what you need, and our team will help you buy a car from Japan through a simple process.</p>
                    <a class="btn btn--light" href="{{ route('register') }}">Register Now</a>
                </div>
                <img src="{{ $assetBase }}/img/register-lexus-lc500-cutout.png" alt="Pearl white Lexus LC 500 luxury coupe facing right">
            </div>
        </section>

        <section class="section">
            <div class="container">
                <p class="eyebrow">Find out Now</p>
                <h2>Latest Guides for Car Buyers</h2>
                <div class="rail-slider" data-rail-slider>
                    <button class="rail-arrow rail-arrow--prev" type="button" data-rail-prev aria-label="Previous blog">‹</button>
                    <div class="blog-grid rail-track">
                        @forelse($homeBlogs as $post)
                            <article class="rail-item">
                                <a href="{{ route('blog.show', $post) }}">
                                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
                                    <span>{{ optional($post->created_at)->format('M d, Y') }}</span>
                                    <h3>{{ $post->title }}</h3>
                                </a>
                            </article>
                        @empty
                            <article class="rail-item"><a href="{{ route('blog') }}"><img src="{{ $assetBase }}/img/blog-accessories.webp" alt="SBK Blog"><span>SBK Blog</span><h3>Add published blogs from admin to show them here.</h3></a></article>
                        @endforelse
                    </div>
                    <button class="rail-arrow rail-arrow--next" type="button" data-rail-next aria-label="Next blog">›</button>
                </div>
                <div class="center"><a class="btn" href="{{ route('blog') }}">View More</a></div>
            </div>
        </section>

        <section class="section faq">
            <div class="container faq__grid">
                <div>
                    <h2>Frequently Asked Questions</h2>
                    @foreach($faqs as $faq)
                        <details><summary>{{ $faq }}</summary><p>Our team can guide you with details and next steps. Contact SBK for current requirements and availability.</p></details>
                    @endforeach
                </div>
                <img src="{{ $assetBase }}/img/home-cutout-santafe-2025.png" alt="FAQ vehicle">
            </div>
        </section>
    

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sbkHomeFilter');
    const brand = document.getElementById('homeBrand');
    const model = document.getElementById('homeModel');
    const price = document.getElementById('homePrice');
    const minPrice = document.getElementById('homeMinPrice');
    const maxPrice = document.getElementById('homeMaxPrice');

    function filterModels() {
        if (!brand || !model) return;

        [...model.options].forEach(option => {
            option.hidden = option.value && brand.value && option.dataset.brand !== brand.value;
        });

        if (model.selectedOptions[0] && model.selectedOptions[0].hidden) {
            model.value = '';
        }
    }

    brand && brand.addEventListener('change', filterModels);
    filterModels();

    form && form.addEventListener('submit', function () {
        if (!price || !minPrice || !maxPrice) return;

        const parts = price.value.split('-');
        minPrice.value = parts[0] || '';
        maxPrice.value = parts[1] || '';
        price.disabled = true;

        if (brand && !brand.value) {
            brand.disabled = true;
        }
    });
});
</script>
  
@endsection
