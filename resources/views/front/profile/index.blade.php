@extends('front.layouts.app')

@php $assetBase = asset('assets/front'); @endphp

@section('title', 'My Profile | SBK Global Auto Trading')
@section('content')
<style>
.sbk-profile{background:#f5f6f8;color:#111216}
.sbk-profile .container{width:min(1180px,calc(100% - 36px));margin:auto}
.sbk-profile-hero{padding:46px 0 92px;background:linear-gradient(120deg,#08090b 0%,#141518 60%,#3a0d10 100%);color:#fff}
.sbk-profile-hero span{display:block;color:#ff4a52;font-size:11px;font-weight:900;letter-spacing:1.5px;text-transform:uppercase}
.sbk-profile-hero h1{margin:8px 0 0;color:#fff;font-size:clamp(28px,4vw,44px);line-height:1.08}
.sbk-profile-hero p{max-width:620px;margin:10px 0 0;color:rgba(255,255,255,.72);font-size:14px;line-height:1.6}
.sbk-profile-body{margin-top:-62px;padding-bottom:96px}
.sbk-alert{margin-bottom:16px;padding:13px 16px;border-radius:8px;font-size:14px}
.sbk-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}
.sbk-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}
.sbk-tab-radio{position:absolute;opacity:0;pointer-events:none}
.sbk-profile-layout{display:grid;grid-template-columns:290px minmax(0,1fr);gap:24px;align-items:start}

/* account + tabs */
.sbk-side{position:sticky;top:110px;border:1px solid #e7e8eb;border-radius:12px;background:#fff;box-shadow:0 18px 50px rgba(17,18,20,.08);overflow:hidden}
.sbk-me{display:grid;justify-items:center;gap:4px;padding:26px 22px 20px;text-align:center;border-bottom:1px solid #eef0f3}
.sbk-me__avatar{width:84px;height:84px;margin-bottom:8px;border-radius:50%;object-fit:cover;background:#f2f3f5;border:3px solid #fff;box-shadow:0 8px 22px rgba(0,0,0,.14)}
.sbk-me__text{min-width:0;display:grid;gap:3px}
.sbk-me__text strong{font-size:18px;line-height:1.25;overflow-wrap:anywhere}
.sbk-me__text span{color:#5f6672;font-size:13px;overflow-wrap:anywhere}
.sbk-me__text small{color:#8a909a;font-size:12px}
.sbk-me__logout{margin:10px 0 0}
.sbk-me__logout button{display:inline-flex;align-items:center;gap:6px;min-height:34px;padding:0 14px;border:1px solid #ffd4d4;border-radius:999px;background:#fff6f6;color:#c4141b;font-size:12px;font-weight:800;cursor:pointer}
.sbk-me__logout button:hover{background:#ffeaea}
.sbk-tabs{display:grid;gap:6px;padding:14px}
.sbk-tabs label{display:flex;align-items:center;gap:10px;min-height:46px;padding:0 14px;border-radius:9px;color:#2a2d33;font-size:14px;font-weight:700;cursor:pointer;transition:background .15s,color .15s}
.sbk-tabs label:hover{background:#f5f6f8}
.sbk-tabs label svg{flex:0 0 auto;width:18px;height:18px;color:#9aa0a8}
.sbk-tabs label b{margin-left:auto;min-width:24px;padding:2px 8px;border-radius:999px;background:#f0f1f4;color:#5f6672;font-size:11px;text-align:center}

/* panels */
.sbk-content-panel{display:none;border:1px solid #e7e8eb;border-radius:12px;background:#fff;box-shadow:0 18px 50px rgba(17,18,20,.08);overflow:hidden}
.sbk-panel-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:18px 24px;border-bottom:1px solid #eef0f3}
.sbk-panel-head span{display:block;color:#d71920;font-size:11px;font-weight:900;letter-spacing:1.3px;text-transform:uppercase}
.sbk-panel-head h2{margin:4px 0 0;color:#111216;font-size:22px;line-height:1.2}
.sbk-panel-head p{margin:0;color:#6b7079;font-size:13px}
.sbk-crm-body{padding:22px}

/* the chosen tab */
#profileTabInvoices:checked~.sbk-profile-layout .sbk-content-invoices,
#profileTabOrders:checked~.sbk-profile-layout .sbk-content-orders,
#profileTabEdit:checked~.sbk-profile-layout .sbk-content-edit{display:block;animation:sbkFade .25s ease both}
#profileTabInvoices:checked~.sbk-profile-layout label[for=profileTabInvoices],
#profileTabOrders:checked~.sbk-profile-layout label[for=profileTabOrders],
#profileTabEdit:checked~.sbk-profile-layout label[for=profileTabEdit]{background:#d71920;color:#fff;box-shadow:0 10px 22px rgba(215,25,32,.22)}
#profileTabInvoices:checked~.sbk-profile-layout label[for=profileTabInvoices] svg,
#profileTabOrders:checked~.sbk-profile-layout label[for=profileTabOrders] svg,
#profileTabEdit:checked~.sbk-profile-layout label[for=profileTabEdit] svg{color:#fff}
#profileTabInvoices:checked~.sbk-profile-layout label[for=profileTabInvoices] b,
#profileTabOrders:checked~.sbk-profile-layout label[for=profileTabOrders] b{background:rgba(255,255,255,.22);color:#fff}
#profileTabInvoices:focus-visible~.sbk-profile-layout label[for=profileTabInvoices],
#profileTabOrders:focus-visible~.sbk-profile-layout label[for=profileTabOrders],
#profileTabEdit:focus-visible~.sbk-profile-layout label[for=profileTabEdit]{outline:2px solid #d71920;outline-offset:2px}
@keyframes sbkFade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}

/* profile form */
.sbk-form{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:22px 24px}
.sbk-form+.sbk-form{border-top:1px solid #eef0f3}
.sbk-form h3{grid-column:1/-1;margin:0;font-size:17px}
.sbk-field{display:grid;gap:7px}
.sbk-field--full{grid-column:1/-1}
.sbk-field label{color:#3b3f46;font-size:12px;font-weight:800;letter-spacing:.4px;text-transform:uppercase}
.sbk-field input{width:100%;height:46px;border:1px solid #dcdde1;border-radius:8px;background:#fff;padding:0 13px;font:inherit;font-size:14px}
.sbk-field input[type=file]{height:auto;padding:10px 12px}
.sbk-field input:focus{border-color:#d71920;outline:none;box-shadow:0 0 0 3px rgba(215,25,32,.1)}
.sbk-submit{width:max-content;min-height:46px;border:0;border-radius:8px;background:#d71920;color:#fff;padding:0 24px;font-size:13px;font-weight:800;box-shadow:0 12px 24px rgba(215,25,32,.2);cursor:pointer}
.sbk-submit:hover{background:#b8141a}

/* website orders */
.sbk-orders{display:grid;gap:16px;padding:22px}
.sbk-order{border:1px solid #e7e8eb;border-radius:10px;overflow:hidden}
.sbk-order-top{display:grid;grid-template-columns:1fr auto;gap:14px;align-items:center;padding:14px 18px;background:#f7f7f8}
.sbk-order-top h3{margin:0;font-size:17px}
.sbk-order-top p{margin:4px 0 0;color:#666;font-size:13px}
.sbk-order-total{text-align:right;font-weight:900}
.sbk-order-items{display:grid;gap:14px;padding:16px 18px}
.sbk-order-item{display:grid;grid-template-columns:128px minmax(0,1fr) auto;gap:16px;align-items:start;padding:14px;border:1px solid #eceef2;border-radius:8px;background:#fff}
.sbk-order-item img{width:128px;height:92px;object-fit:cover;border-radius:7px;background:#f3f3f3}
.sbk-order-item h4{margin:0 0 6px;font-size:16px;line-height:1.25}
.sbk-order-item p{margin:0;color:#666;font-size:13px}
.sbk-order-price{text-align:right;font-size:15px}
.sbk-vehicle-specs{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:12px}
.sbk-vehicle-specs span{display:grid;gap:3px;min-height:48px;padding:8px 10px;border-radius:6px;background:#f7f7f8;color:#111;font-size:12px;overflow-wrap:anywhere}
.sbk-vehicle-specs small{color:#777;font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.5px}
.sbk-empty{display:grid;justify-items:center;gap:8px;padding:44px 24px;color:#6b7079;text-align:center;line-height:1.6}
.sbk-empty svg{width:40px;height:40px;color:#c4c8cf}

@media(max-width:991px){
  .sbk-profile-layout{grid-template-columns:1fr;gap:16px}
  .sbk-side{position:static}
  .sbk-me{grid-template-columns:auto minmax(0,1fr) auto;justify-items:start;align-items:center;gap:12px;padding:16px;text-align:left}
  .sbk-me__avatar{width:52px;height:52px;margin:0;border-width:2px}
  .sbk-me__text strong{font-size:16px}
  .sbk-me__logout{margin:0}
  .sbk-me__logout button span{display:none}
  .sbk-me__logout button{width:38px;height:38px;padding:0;justify-content:center}
  .sbk-tabs{grid-auto-flow:column;grid-auto-columns:minmax(0,1fr);gap:6px;padding:8px}
  .sbk-tabs label{justify-content:center;gap:6px;min-height:42px;padding:0 6px;font-size:13px;text-align:center}
  .sbk-tabs label b{margin-left:2px;min-width:0;padding:1px 6px}
  .sbk-vehicle-specs{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:640px){
  .sbk-profile .container{width:calc(100% - 24px)}
  .sbk-profile-hero{padding:24px 0 70px}
  .sbk-profile-hero p{display:none}
  .sbk-profile-body{margin-top:-52px;padding-bottom:84px}
  .sbk-tabs label svg{display:none}
  .sbk-profile-hero h1{font-size:26px!important}
  .sbk-panel-head{padding:14px 16px}
  .sbk-panel-head h2{font-size:19px!important;line-height:1.25!important}
  .sbk-crm-body{padding:12px}
  .sbk-form{grid-template-columns:1fr;padding:18px 16px}
  .sbk-submit{width:100%}
  .sbk-orders{padding:12px}
  .sbk-order-top{grid-template-columns:1fr}
  .sbk-order-total{text-align:left}
  .sbk-order-item{grid-template-columns:1fr}
  .sbk-order-item img{width:100%;height:180px}
  .sbk-order-price{text-align:left}
}
@media(prefers-reduced-motion:reduce){.sbk-content-panel{animation:none!important}}
</style>

@php
    // Customers with CRM invoices come here for them, so that tab opens first;
    // a profile form that came back with errors opens its own tab.
    $invoiceCount = $crm ? (int) data_get($crm, 'record.totals.invoices', 0) : 0;
    $tabs = $crm ? ['invoices', 'orders', 'edit'] : ['orders', 'edit'];
    $profileTab = $errors->any() ? 'edit' : (in_array(request('tab'), $tabs, true) ? request('tab') : ($crm ? 'invoices' : 'edit'));
@endphp

<section class="sbk-profile">
    <div class="sbk-profile-hero">
        <div class="container">
            <span>My account</span>
            <h1>Welcome, {{ $user->first_name ?: ($user->name ?: 'SBK Customer') }}</h1>
            <p>Your invoices, payments, vehicle photos and documents, and your website orders - all in one place.</p>
        </div>
    </div>

    <div class="container sbk-profile-body">
        @if(session('success'))
            <div class="sbk-alert sbk-alert--success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="sbk-alert sbk-alert--error">{{ $errors->first() ?: 'Please check the profile fields and try again.' }}</div>
        @endif

        @if($crm)
            <input class="sbk-tab-radio" type="radio" name="profile_tab" id="profileTabInvoices" {{ $profileTab === 'invoices' ? 'checked' : '' }}>
        @endif
        <input class="sbk-tab-radio" type="radio" name="profile_tab" id="profileTabOrders" {{ $profileTab === 'orders' ? 'checked' : '' }}>
        <input class="sbk-tab-radio" type="radio" name="profile_tab" id="profileTabEdit" {{ $profileTab === 'edit' ? 'checked' : '' }}>

        <div class="sbk-profile-layout">
            <aside class="sbk-side">
                <div class="sbk-me">
                    <img class="sbk-me__avatar" src="{{ $user->profile_picture_url }}" alt="{{ $user->name ?: $user->email }}">
                    <div class="sbk-me__text">
                        <strong>{{ $user->name ?: 'SBK Customer' }}</strong>
                        <span>{{ $user->email }}</span>
                        @if($user->phone || $user->country)
                            <small>{{ implode(' · ', array_filter([$user->phone, $user->country])) }}</small>
                        @endif
                    </div>
                    <form class="sbk-me__logout" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" aria-label="Logout">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
                <nav class="sbk-tabs" aria-label="My account">
                    @if($crm)
                        <label for="profileTabInvoices">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></svg>
                            Invoices @if($invoiceCount)<b>{{ $invoiceCount }}</b>@endif
                        </label>
                    @endif
                    <label for="profileTabOrders">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                        Orders @if($orders->count())<b>{{ $orders->count() }}</b>@endif
                    </label>
                    <label for="profileTabEdit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Profile
                    </label>
                </nav>
            </aside>

            <div>
                @if($crm)
                    <div class="sbk-content-panel sbk-content-invoices">
                        <div class="sbk-panel-head">
                            <div>
                                <span>Your purchases</span>
                                <h2>Invoices &amp; vehicles</h2>
                            </div>
                            <p>Payments, photos and documents for every car you bought.</p>
                        </div>
                        <div class="sbk-crm-body">
                            @include('partials.crm.record', ['crm' => $crm])
                        </div>
                    </div>
                @endif

                <div class="sbk-content-panel sbk-content-orders">
                    <div class="sbk-panel-head">
                        <div>
                            <span>Website orders</span>
                            <h2>Vehicle orders</h2>
                        </div>
                    </div>
                    @if($orders->isEmpty())
                        <div class="sbk-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><path d="M3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
                            No order placed on the website yet.
                        </div>
                    @else
                        <div class="sbk-orders">
                            @foreach($orders as $order)
                                <div class="sbk-order">
                                    <div class="sbk-order-top">
                                        <div>
                                            <h3>{{ $order->order_no }}</h3>
                                            <p>{{ optional($order->order_date ? \Carbon\Carbon::parse($order->order_date) : $order->created_at)->format('d M Y') }} / {{ ucfirst($order->status) }}</p>
                                        </div>
                                        <div class="sbk-order-total">
                                            Paid ${{ number_format($order->paid_amount ?? $order->subtotal, 2) }}<br>
                                            <span style="font-size:12px;color:#666">Remaining ${{ number_format($order->remaining_amount ?? max(($order->subtotal ?? 0) - ($order->paid_amount ?? 0), 0), 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="sbk-order-items">
                                        @foreach($order->items as $item)
                                            @php
                                                $vehicle = $item->product;
                                                $brand = $item->brand ?: optional(optional($vehicle)->brand)->name;
                                                $model = $item->model ?: optional(optional($vehicle)->vehicleModel)->name;
                                                $specs = [
                                                    'Year' => optional($vehicle)->year,
                                                    'Mileage' => optional($vehicle)->mileage ? number_format($vehicle->mileage) . ' km' : null,
                                                    'Fuel' => optional($vehicle)->fuel_type,
                                                    'Transmission' => optional($vehicle)->transmission,
                                                    'Engine' => optional($vehicle)->engine,
                                                    'Color' => optional($vehicle)->color,
                                                    'Steering' => optional($vehicle)->steering,
                                                    'Chassis' => optional($vehicle)->chassis_no,
                                                ];
                                            @endphp
                                            <div class="sbk-order-item">
                                                <img src="{{ optional($vehicle)->primary_image_url ?? asset('assets/front/img/car-markx-white.jpg') }}" alt="{{ $item->product_title }}">
                                                <div>
                                                    <h4>{{ $item->product_title }}</h4>
                                                    <p>{{ trim(($brand ?? '') . ' ' . ($model ?? '')) ?: 'Vehicle details' }} / Qty {{ $item->quantity }}</p>
                                                    <div class="sbk-vehicle-specs">
                                                        @foreach($specs as $label => $value)
                                                            @if($value)
                                                                <span><small>{{ $label }}</small>{{ $value }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <strong class="sbk-order-price">${{ number_format($item->price * $item->quantity, 2) }}</strong>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="sbk-content-panel sbk-content-edit">
                    <div class="sbk-panel-head">
                        <div>
                            <span>Profile details</span>
                            <h2>Edit profile</h2>
                        </div>
                    </div>
                    <form class="sbk-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="sbk-field sbk-field--full">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="sbk-field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="sbk-field">
                            <label for="phone">Phone</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" required>
                        </div>
                        <div class="sbk-field">
                            <label for="country">Country</label>
                            <input id="country" name="country" type="text" value="{{ old('country', $user->country) }}" required>
                        </div>
                        <div class="sbk-field">
                            <label for="profile_picture">Profile Picture</label>
                            <input id="profile_picture" name="profile_picture" type="file" accept="image/*">
                        </div>
                        <div class="sbk-field sbk-field--full">
                            <button class="sbk-submit" type="submit">Update Profile</button>
                        </div>
                    </form>

                    <form class="sbk-form" method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        <h3>Change password</h3>
                        <div class="sbk-field sbk-field--full">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                        </div>
                        <div class="sbk-field">
                            <label for="password">New Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required>
                        </div>
                        <div class="sbk-field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                        </div>
                        <div class="sbk-field sbk-field--full">
                            <button class="sbk-submit" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
