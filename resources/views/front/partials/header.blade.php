@php
    $cartCount = \App\Models\CartItem::where('session_id', request()->session()->getId())->sum('quantity');
    $wishlistCount = \App\Models\WishlistItem::where('session_id', request()->session()->getId())->count();
    $frontUser = auth()->user();
    $frontUserHasPhoto = $frontUser && $frontUser->profile_picture && file_exists(uploadsDir('front') . $frontUser->profile_picture);
@endphp

<!-- =========================
     EXISTING HEADER
     Only Brand has been changed
========================= -->

<header class="topbar">
    <div class="container topbar__inner">
        <div class="topbar__left">
            <span>+81 70-9427-5106</span>
            <span>info@sbkautotrading.com</span>
            <span>Mon - Fri: 10:00 - 18:00</span>
        </div>

        <div class="topbar__right">
            <div class="topbar__clocks">
                <span class="is-active"
                      data-clock="America/Chicago"
                      data-label="USA (MN)">
                    USA (MN) --:--:--
                </span>

                <span data-clock="America/Jamaica"
                      data-label="JAMAICA">
                    JAMAICA --:--:--
                </span>

                <span data-clock="Africa/Nairobi"
                      data-label="KENYA">
                    KENYA --:--:--
                </span>

                <span data-clock="Asia/Dubai"
                      data-label="UAE">
                    UAE --:--:--
                </span>

                <span data-clock="Asia/Tokyo"
                      data-label="JAPAN">
                    JAPAN --:--:--
                </span>
            </div>

            <div class="currency-converter" data-currency-converter>
                <label>
                    <span>USD</span>
                    <input type="number" min="0" step="1" value="1" data-usd-input aria-label="US dollars">
                </label>
                <span class="currency-converter__swap">=</span>
                <label>
                    <span>JPY</span>
                    <input type="number" min="0" step="1" value="" placeholder="..." data-jpy-input aria-label="Japanese yen">
                </label>
            </div>
        </div>
    </div>
</header>


<nav class="navbar">

    <div class="container navgrid">

        <!-- LOGO -->
        <a class="logo" href="{!! route('index') !!}">
            <img
                src="{{ $assetBase }}/img/logo.png"
                alt="SBK Global Auto Trading"
            >
        </a>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="frontNavMenu" aria-label="Open navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>


        <!-- NAVIGATION -->
        <div class="navlinks" id="frontNavMenu">

            <a href="{!! route('index') !!}" class="{{ request()->routeIs('index') ? 'is-active' : '' }}">Home</a>
            <div class="sbk-about-menu">
                <a href="{!! route('about-us') !!}" class="sbk-about-trigger {{ request()->routeIs('about-us', 'official-partners') ? 'is-active' : '' }}" aria-haspopup="true">
                    About Us
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>
                </a>
                <div class="sbk-about-dropdown">
                    <a href="{!! route('about-us') !!}" class="{{ request()->routeIs('about-us') ? 'is-active' : '' }}">About SBK</a>
                    <a href="{!! route('official-partners') !!}" class="{{ request()->routeIs('official-partners') ? 'is-active' : '' }}">Official Partners</a>
                </div>
            </div>
            <a href="{!! route('shop') !!}" class="{{ request()->routeIs('shop') ? 'is-active' : '' }}">Shop</a>
            <a href="{!! route('services') !!}" class="{{ request()->routeIs('services') ? 'is-active' : '' }}">Services</a>


            <!-- ==========================================
                 BRAND MEGA MENU
            =========================================== -->
            <div class="sbk-brand-menu">

                <a href="{{ route('shop') }}"
                   class="sbk-brand-trigger"
                   aria-haspopup="true"
                   aria-expanded="false">

                    Brand

                    <svg viewBox="0 0 24 24">
                        <path d="M6 9l6 6 6-6"/>
                    </svg>

                </a>


                <!-- MEGA MENU -->
                <div class="sbk-brand-dropdown">

                    <div class="sbk-brand-dropdown-inner">

                        <!-- HEADER -->
                        <div class="sbk-brand-dropdown-header">

                            <div>
                                <span class="sbk-brand-small">
                                    EXPLORE VEHICLES
                                </span>

                                <h3>Browse By Brand</h3>
                            </div>

                            <a href="{{ route('shop') }}" class="sbk-view-brands">
                                View All Brands
                                <span>→</span>
                            </a>

                        </div>


                        <!-- BRAND GRID -->
                        <div class="sbk-brand-grid">


                            <!-- TOYOTA -->
                            <a href="{{ route('shop', ['brands' => ['Toyota']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-toyota.webp"
                                        alt="Toyota"
                                    >
                                </div>

                                <span>Toyota</span>

                            </a>


                            <!-- HONDA -->
                            <a href="{{ route('shop', ['brands' => ['Honda']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-honda.webp"
                                        alt="Honda"
                                    >
                                </div>

                                <span>Honda</span>

                            </a>


                            <!-- NISSAN -->
                            <a href="{{ route('shop', ['brands' => ['Nissan']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-nissan.webp"
                                        alt="Nissan"
                                    >
                                </div>

                                <span>Nissan</span>

                            </a>


                            <!-- SUZUKI -->
                            <a href="{{ route('shop', ['brands' => ['Suzuki']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-suzuki.webp"
                                        alt="Suzuki"
                                    >
                                </div>

                                <span>Suzuki</span>

                            </a>


                            <!-- MAZDA -->
                            <a href="{{ route('shop', ['brands' => ['Mazda']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-mazda.webp"
                                        alt="Mazda"
                                    >
                                </div>

                                <span>Mazda</span>

                            </a>


                            <!-- MITSUBISHI -->
                            <a href="{{ route('shop', ['brands' => ['Mitsubishi']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-mitsubishi.webp"
                                        alt="Mitsubishi"
                                    >
                                </div>

                                <span>Mitsubishi</span>

                            </a>


                            <!-- SUBARU -->
                            <a href="{{ route('shop', ['brands' => ['Subaru']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-subaru.webp"
                                        alt="Subaru"
                                    >
                                </div>

                                <span>Subaru</span>

                            </a>


                            <!-- LEXUS -->
                            <a href="{{ route('shop', ['brands' => ['Lexus']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-lexus.webp"
                                        alt="Lexus"
                                    >
                                </div>

                                <span>Lexus</span>

                            </a>


                            <!-- BMW -->
                            <a href="{{ route('shop', ['brands' => ['BMW']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-bmw.webp"
                                        alt="BMW"
                                    >
                                </div>

                                <span>BMW</span>

                            </a>


                            <!-- MERCEDES -->
                            <a href="{{ route('shop', ['brands' => ['Mercedes-Benz']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-mercedes.webp"
                                        alt="Mercedes-Benz"
                                    >
                                </div>

                                <span>Mercedes-Benz</span>

                            </a>


                            <!-- VOLKSWAGEN -->
                            <a href="{{ route('shop', ['brands' => ['Volkswagen']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-volkswagen.webp"
                                        alt="Volkswagen"
                                    >
                                </div>

                                <span>Volkswagen</span>

                            </a>


                            <!-- AUDI -->
                            <a href="{{ route('shop', ['brands' => ['Audi']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-audi.webp"
                                        alt="Audi"
                                    >
                                </div>

                                <span>Audi</span>

                            </a>


                            <!-- VOLVO -->
                            <a href="{{ route('shop', ['brands' => ['Volvo']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-volvo.webp"
                                        alt="Volvo"
                                    >
                                </div>

                                <span>Volvo</span>

                            </a>


                            <!-- FORD -->
                            <a href="{{ route('shop', ['brands' => ['Ford']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-ford.webp"
                                        alt="Ford"
                                    >
                                </div>

                                <span>Ford</span>

                            </a>


                            <!-- JEEP -->
                            <a href="{{ route('shop', ['brands' => ['Jeep']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-jeep.webp"
                                        alt="Jeep"
                                    >
                                </div>

                                <span>Jeep</span>

                            </a>


                            <!-- LAND ROVER -->
                            <a href="{{ route('shop', ['brands' => ['Land Rover']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-land-rover.webp"
                                        alt="Land Rover"
                                    >
                                </div>

                                <span>Land Rover</span>

                            </a>


                            <!-- HYUNDAI -->
                            <a href="{{ route('shop', ['brands' => ['Hyundai']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-hyundai.webp"
                                        alt="Hyundai"
                                    >
                                </div>

                                <span>Hyundai</span>

                            </a>


                            <!-- KIA -->
                            <a href="{{ route('shop', ['brands' => ['Alfa Romeo']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-alfa-romeo.webp"
                                        alt="alfa-romeo"
                                    >
                                </div>

                                <span>Alfa Romeo</span>

                            </a>


                            <!-- DAIHATSU -->
                            <a href="{{ route('shop', ['brands' => ['Daihatsu']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-daihatsu.webp"
                                        alt="Daihatsu"
                                    >
                                </div>

                                <span>Daihatsu</span>

                            </a>


                            <!-- HINO -->
                            <a href="{{ route('shop', ['brands' => ['Hino']]) }}" class="sbk-brand-item">

                                <div class="sbk-brand-logo">
                                    <img
                                        src="{{ $assetBase }}/img/brand/brand-hino.webp"
                                        alt="Hino"
                                    >
                                </div>

                                <span>Hino</span>

                            </a>


                            <!-- PORSCHE -->
                           


                        </div>

                    </div>

                </div>
                <!-- END MEGA MENU -->

            </div>
            <!-- END BRAND -->


            <a href="{!! route('blog') !!}" class="{{ request()->routeIs('blog') ? 'is-active' : '' }}">Blogs</a>
           
            <a href="{!! route('our-locations') !!}" class="{{ request()->routeIs('our-locations') ? 'is-active' : '' }}">Our Locations</a>
            <a href="{!! route('contact-us') !!}" class="{{ request()->routeIs('contact-us', 'contact-us.submit') ? 'is-active' : '' }}">Contact Us</a>

        </div>


        <!-- NAV ACTIONS -->
        <div class="navactions">

            <form class="sbk-search" action="{{ route('shop') }}" method="GET" autocomplete="off">
                <input
                    id="sbkSearchInput"
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search here..."
                    aria-label="Search products"
                >
                <div class="sbk-search-suggestions" id="sbkSearchSuggestions"></div>
            </form>

            <!-- <a href="#">Live chat</a> -->

            <a class="sbk-head-icon {{ request()->routeIs('wishlist.*') ? 'is-active' : '' }}" href="{{ route('wishlist.index') }}" aria-label="Wishlist" title="Wishlist">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.8 5.7a5.2 5.2 0 0 0-7.4 0L12 7.1l-1.4-1.4a5.2 5.2 0 0 0-7.4 7.4L12 21l8.8-7.9a5.2 5.2 0 0 0 0-7.4Z"/></svg>
                @if($wishlistCount > 0)<b>{{ $wishlistCount }}</b>@endif
            </a>

            @auth
                <div class="sbk-account-menu">
                    <button class="sbk-account-toggle {{ request()->routeIs('profile.*') ? 'is-active' : '' }}" type="button" aria-expanded="false" aria-haspopup="true" aria-label="Account menu">
                        @if($frontUserHasPhoto)
                            <img src="{{ $frontUser->profile_picture_url }}" alt="{{ $frontUser->name ?: $frontUser->email }}">
                        @else
                            <span class="sbk-account-fallback" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M12 12.4a4.4 4.4 0 1 0 0-8.8 4.4 4.4 0 0 0 0 8.8Z"/><path d="M4.2 20.2c1.3-3.8 4.1-6 7.8-6s6.5 2.2 7.8 6"/></svg>
                            </span>
                        @endif
                    </button>
                    <div class="sbk-account-dropdown">
                        <div class="sbk-account-head">
                            @if($frontUserHasPhoto)
                                <img src="{{ $frontUser->profile_picture_url }}" alt="">
                            @else
                                <span class="sbk-account-fallback sbk-account-fallback--small" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M12 12.4a4.4 4.4 0 1 0 0-8.8 4.4 4.4 0 0 0 0 8.8Z"/><path d="M4.2 20.2c1.3-3.8 4.1-6 7.8-6s6.5 2.2 7.8 6"/></svg>
                                </span>
                            @endif
                            <div>
                                <strong>{{ $frontUser->name ?: 'SBK Customer' }}</strong>
                                <span>{{ $frontUser->email }}</span>
                            </div>
                        </div>
                        <a href="{{ route('profile.index') }}">Edit Profile</a>
                        <a href="{{ route('profile.index', ['tab' => 'orders']) }}">Orders</a>
                        @if($frontUser->hasCrmRecord())
                            <a href="{{ route('profile.index', ['tab' => 'invoices']) }}">My Invoices</a>
                        @endif
                        <a href="{{ route('chat.app') }}" data-sbk-chat>Live chat</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a class="sbk-auth-link {{ request()->routeIs('login') ? 'is-active' : '' }}" href="{{ route('login') }}">Login</a>
                <!-- <a class="sbk-auth-link sbk-auth-link--red {{ request()->routeIs('register') ? 'is-active' : '' }}" href="{{ route('register') }}">Sign Up</a> -->
            @endauth

            <a class="sbk-head-icon {{ request()->routeIs('cart.*') ? 'is-active' : '' }}" href="{{ route('cart.index') }}" aria-label="Cart" title="Cart">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h2l2.2 10.2a2 2 0 0 0 2 1.6h6.9a2 2 0 0 0 1.9-1.4L21 8H7"/><path d="M10 21h.01M18 21h.01"/></svg>
                @if($cartCount > 0)<b>{{ $cartCount }}</b>@endif
            </a>

            <button>Buy Now</button>

        </div>

    </div>

</nav>

<style>
.navactions .sbk-head-icon{position:relative;display:inline-grid!important;place-items:center;width:46px;height:46px;margin:0!important;border:1px solid #e5e7eb;border-radius:50%;background:#fff;color:#111!important;text-decoration:none!important;line-height:1;box-shadow:0 8px 22px rgba(17,18,22,.08);transition:background .2s ease,border-color .2s ease,color .2s ease,transform .2s ease}
.navactions .sbk-head-icon svg{width:21px;height:21px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.navactions .sbk-head-icon:hover,.navactions .sbk-head-icon.is-active{background:#fff1f1;color:#d50000!important;border-color:#ffd5d5;transform:translateY(-1px)}
.navactions .sbk-head-icon b{position:absolute;right:-5px;top:-6px;min-width:20px;height:20px;padding:0 6px;border-radius:999px;background:#d50000;color:#fff;font-size:11px;font-weight:900;line-height:20px;text-align:center}
.sbk-auth-link{display:inline-flex!important;align-items:center;justify-content:center;min-height:42px;margin:0!important;padding:0 15px;border:1px solid #e5e7eb;border-radius:999px;background:#fff;color:#111!important;text-decoration:none!important;font-size:12px;font-weight:900;text-transform:uppercase;white-space:nowrap;box-shadow:0 8px 22px rgba(17,18,22,.07)}
.sbk-auth-link:hover,.sbk-auth-link.is-active{border-color:#ffd5d5;background:#fff1f1;color:#d50000!important}
.sbk-auth-link--red{border-color:#d50000;background:#d50000;color:#fff!important}
.sbk-auth-link--red:hover,.sbk-auth-link--red.is-active{background:#111;color:#fff!important;border-color:#111}
.sbk-account-menu{position:relative;display:inline-flex;align-items:center}
.navactions button.sbk-account-toggle{display:inline-grid!important;grid-column:auto!important;grid-row:auto!important;place-items:center!important;width:46px!important;height:46px!important;min-width:46px!important;max-width:46px!important;min-height:46px!important;padding:0!important;border:1px solid #e5e7eb!important;border-radius:50%!important;background:#fff!important;background-color:#fff!important;background-image:none!important;color:#111!important;box-shadow:0 8px 22px rgba(17,18,22,.08)!important;cursor:pointer;overflow:hidden}
.sbk-account-toggle img{display:block;width:100%;height:100%;object-fit:cover;border-radius:50%;background:#f7f8fa}
.sbk-account-fallback{position:relative;display:grid;width:100%;height:100%;place-items:center;border-radius:50%;background:linear-gradient(145deg,#fff,#f1f3f6);color:#1d1f23}
.sbk-account-fallback:after{content:"";position:absolute;right:5px;bottom:5px;width:10px;height:10px;border:2px solid #fff;border-radius:50%;background:#d50000}
.sbk-account-fallback svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}
.sbk-account-fallback--small{width:44px;height:44px;box-shadow:inset 0 0 0 1px #edf0f3}
.sbk-account-fallback--small:after{width:8px;height:8px;right:4px;bottom:4px}
.sbk-account-fallback--small svg{width:23px;height:23px}
.sbk-account-toggle:hover,.sbk-account-toggle.is-active{box-shadow:0 0 0 3px rgba(213,0,0,.14),0 8px 24px rgba(17,18,22,.15)}
.sbk-account-dropdown{position:absolute;right:0;top:calc(100% + 12px);z-index:10000;display:none;width:250px;border:1px solid #ececec;border-radius:9px;background:#fff;box-shadow:0 22px 55px rgba(0,0,0,.18);overflow:hidden}
.sbk-account-menu:hover .sbk-account-dropdown,.sbk-account-menu.is-open .sbk-account-dropdown{display:block}
.sbk-account-head{display:grid;grid-template-columns:44px 1fr;gap:11px;align-items:center;padding:14px;border-bottom:1px solid #f0f0f0;background:#fafafa}
.sbk-account-head img{width:44px;height:44px;border-radius:50%;object-fit:cover;background:#f3f4f6}
.sbk-account-head strong{display:block;color:#111;font-size:13px;line-height:1.25}
.sbk-account-head span{display:block;margin-top:3px;color:#777;font-size:11px;line-height:1.3;word-break:break-word}
.sbk-account-dropdown a,.sbk-account-dropdown button{display:flex!important;width:100%;min-height:43px;align-items:center;margin:0!important;padding:0 15px;border:0;border-bottom:1px solid #f3f3f3;background:#fff;color:#111!important;text-align:left;text-decoration:none!important;font:inherit;font-size:13px;font-weight:900;cursor:pointer}
.sbk-account-dropdown a:hover,.sbk-account-dropdown button:hover{background:#fff4f4;color:#d50000!important}
.sbk-account-dropdown form{margin:0}
.sbk-search{position:relative;margin:0}
.sbk-search input{width:170px}
.sbk-search-suggestions{display:none;position:absolute;right:0;top:calc(100% + 10px);z-index:9999;width:330px;max-width:82vw;border:1px solid #ececec;border-radius:8px;background:#fff;box-shadow:0 18px 45px rgba(0,0,0,.16);overflow:hidden}
.sbk-search-suggestions.is-open{display:block}
.sbk-search-suggestions a{display:grid!important;grid-template-columns:54px 1fr;gap:10px;align-items:center;margin:0!important;padding:10px!important;color:#111!important;text-decoration:none!important;border-bottom:1px solid #f0f0f0;font-size:12px;line-height:1.35}
.sbk-search-suggestions a:last-child{border-bottom:0}
.sbk-search-suggestions a:hover{background:#fff4f4!important;color:#111!important}
.sbk-search-suggestions img{width:54px;height:42px;object-fit:cover;border-radius:5px;background:#f3f3f3}
.sbk-search-suggestions strong{display:block;font-size:12px;color:#111}
.sbk-search-suggestions span{display:block;margin-top:2px;color:#777;font-size:11px}
.sbk-search-empty{padding:12px 14px;color:#777;font-size:12px}
.sbk-about-menu{position:relative;display:flex;align-items:center}
.navlinks .sbk-about-trigger{display:inline-flex!important;align-items:center;gap:6px}
.sbk-about-trigger svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;transition:transform .2s ease}
.sbk-about-dropdown{position:absolute;left:0;top:calc(100% + 18px);z-index:10001;display:grid;min-width:210px;padding:8px;border:1px solid #ececec;border-radius:8px;background:#fff;box-shadow:0 22px 55px rgba(0,0,0,.18);opacity:0;visibility:hidden;transform:translateY(10px);transition:opacity .2s ease,transform .2s ease,visibility .2s ease}
.sbk-about-dropdown:before{content:"";position:absolute;left:0;right:0;top:-18px;height:18px}
.sbk-about-dropdown a{display:flex!important;align-items:center;min-height:42px;margin:0!important;padding:0 13px!important;border-radius:6px;color:#111!important;text-decoration:none!important;font-size:13px!important;font-weight:900!important;white-space:nowrap}
.sbk-about-dropdown a:hover,.sbk-about-dropdown a.is-active{background:#fff1f1;color:#d50000!important}
.sbk-about-menu:hover .sbk-about-dropdown,.sbk-about-menu:focus-within .sbk-about-dropdown{opacity:1;visibility:visible;transform:translateY(0)}
.sbk-about-menu:hover .sbk-about-trigger svg,.sbk-about-menu:focus-within .sbk-about-trigger svg{transform:rotate(180deg)}
@media(max-width:900px){.sbk-about-menu{display:block;width:100%}.navlinks .sbk-about-trigger{width:100%;justify-content:space-between}.sbk-about-dropdown{position:static;display:grid;min-width:0;margin:0 0 4px 14px;padding:4px 0 0;border:0;box-shadow:none;opacity:1;visibility:visible;transform:none;background:transparent}.sbk-about-dropdown:before{display:none}.sbk-about-dropdown a{min-height:36px!important;padding:0 12px!important;background:transparent!important;font-size:12px!important}.sbk-about-trigger svg{transform:none!important}}
</style>


<!-- ==========================================================
     BRAND MEGA MENU CSS
     This only styles the new Brand dropdown
========================================================== -->


<!-- ==========================================================
     MOBILE CLICK SUPPORT
========================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const brandMenu =
        document.querySelector('.sbk-brand-menu');

    const brandTrigger =
        document.querySelector('.sbk-brand-trigger');


    if (!brandMenu || !brandTrigger) {
        return;
    }


    brandTrigger.addEventListener(
        'click',
        function (event) {

            /*
            Desktop uses hover.
            Tablet/mobile can use click.
            */

            if (window.innerWidth <= 850) {

                event.preventDefault();

                brandMenu.classList.toggle(
                    'is-open'
                );

                brandTrigger.setAttribute(
                    'aria-expanded',
                    brandMenu.classList.contains(
                        'is-open'
                    )
                        ? 'true'
                        : 'false'
                );

            }

        }
    );


    /*
    Close if user clicks outside.
    */

    document.addEventListener(
        'click',
        function (event) {

            if (
                !brandMenu.contains(
                    event.target
                )
            ) {

                brandMenu.classList.remove(
                    'is-open'
                );

                brandTrigger.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }

        }
    );


    /*
    Close with Escape key.
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                brandMenu.classList.remove(
                    'is-open'
                );

                brandTrigger.setAttribute(
                    'aria-expanded',
                    'false'
                );

            }

        }
    );

});

</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const accountMenu = document.querySelector('.sbk-account-menu');
    const accountToggle = document.querySelector('.sbk-account-toggle');

    if (!accountMenu || !accountToggle) {
        return;
    }

    accountToggle.addEventListener('click', function (event) {
        event.preventDefault();
        accountMenu.classList.toggle('is-open');
        accountToggle.setAttribute('aria-expanded', accountMenu.classList.contains('is-open') ? 'true' : 'false');
    });

    document.addEventListener('click', function (event) {
        if (!accountMenu.contains(event.target)) {
            accountMenu.classList.remove('is-open');
            accountToggle.setAttribute('aria-expanded', 'false');
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            accountMenu.classList.remove('is-open');
            accountToggle.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('sbkSearchInput');
    const box = document.getElementById('sbkSearchSuggestions');
    let timer = null;
    let controller = null;

    if (!input || !box) {
        return;
    }

    function closeSuggestions() {
        box.classList.remove('is-open');
        box.innerHTML = '';
    }

    function renderSuggestions(items) {
        if (!items.length) {
            box.innerHTML = '<div class="sbk-search-empty">No matching vehicles found.</div>';
            box.classList.add('is-open');
            return;
        }

        box.innerHTML = items.map(function (item) {
            return '<a href="' + item.url + '"><img src="' + item.image + '" alt=""><div><strong>' + item.title + '</strong><span>' + [item.brand, item.model, item.price].filter(Boolean).join(' / ') + '</span></div></a>';
        }).join('');
        box.classList.add('is-open');
    }

    input.addEventListener('input', function () {
        const query = input.value.trim();
        clearTimeout(timer);

        if (query.length < 2) {
            closeSuggestions();
            return;
        }

        timer = setTimeout(function () {
            if (controller) {
                controller.abort();
            }

            controller = new AbortController();
            fetch('{{ route('search.suggestions') }}?q=' + encodeURIComponent(query), {
                headers: {'Accept': 'application/json'},
                signal: controller.signal
            })
                .then(response => response.json())
                .then(renderSuggestions)
                .catch(function (error) {
                    if (error.name !== 'AbortError') {
                        closeSuggestions();
                    }
                });
        }, 180);
    });

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSuggestions();
        }
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.sbk-search')) {
            closeSuggestions();
        }
    });
});
</script>
