@extends('front.layouts.app')

@php $assetBase = asset('assets/front'); @endphp

@section('title', 'My Profile | SBK Global Auto Trading')
@section('content')
<style>
.sbk-profile{background:#fff;color:#111}.sbk-profile .container{width:min(1180px,calc(100% - 36px));margin:auto}.sbk-profile-hero{padding:70px 0;background:#08090b;color:#fff}.sbk-profile-hero h1{margin:0;color:#fff;font-size:clamp(38px,5vw,64px);line-height:1}.sbk-profile-hero p{max-width:650px;margin:14px 0 0;color:rgba(255,255,255,.76);line-height:1.7}.sbk-profile-body{padding:54px 0 96px}.sbk-alert{margin-bottom:18px;padding:14px 16px;border-radius:7px}.sbk-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}.sbk-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-profile-tabs{display:grid;grid-template-columns:340px minmax(0,1fr);gap:28px;align-items:start}.sbk-tab-radio{position:absolute;opacity:0;pointer-events:none}.sbk-panel{border:1px solid #e7e8eb;border-radius:10px;background:#fff;box-shadow:0 20px 55px rgba(17,18,20,.08);overflow:hidden}.sbk-panel-head{padding:24px 28px;background:#111216;color:#fff}.sbk-panel-head span{display:block;color:#ff4a52;font-size:11px;font-weight:900;letter-spacing:1.5px;text-transform:uppercase}.sbk-panel-head h2{margin:8px 0 0;color:#fff;font-size:28px}.sbk-profile-card{padding:28px;text-align:center}.sbk-avatar{width:142px;height:142px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 10px 28px rgba(0,0,0,.18);background:#f4f4f4}.sbk-profile-card h3{margin:16px 0 4px;font-size:24px}.sbk-profile-card p{margin:0 0 8px;color:#666}.sbk-profile-nav{display:grid;gap:10px;margin-top:24px;text-align:left}.sbk-profile-nav label{display:flex;align-items:center;justify-content:space-between;min-height:48px;padding:0 16px;border:1px solid #e7e8eb;border-radius:7px;background:#f7f7f8;color:#111;font-size:13px;font-weight:900;text-transform:uppercase;cursor:pointer}.sbk-profile-nav label:after{content:">";color:#d71920}.sbk-logout{width:100%;min-height:44px;border:1px solid #ffd1d1;border-radius:6px;background:#fff3f3;color:#c90000;font-size:12px;font-weight:900;text-transform:uppercase;cursor:pointer}.sbk-content-panel{display:none}.sbk-tab-edit:checked~.sbk-profile-tabs .sbk-content-edit,.sbk-tab-orders:checked~.sbk-profile-tabs .sbk-content-orders{display:block}.sbk-tab-edit:checked~.sbk-profile-tabs label[for=profileTabEdit],.sbk-tab-orders:checked~.sbk-profile-tabs label[for=profileTabOrders]{background:#d71920;border-color:#d71920;color:#fff}.sbk-tab-edit:checked~.sbk-profile-tabs label[for=profileTabEdit]:after,.sbk-tab-orders:checked~.sbk-profile-tabs label[for=profileTabOrders]:after{color:#fff}.sbk-form{display:grid;grid-template-columns:1fr 1fr;gap:17px;padding:28px}.sbk-field{display:grid;gap:8px}.sbk-field--full{grid-column:1/-1}.sbk-field label{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.7px}.sbk-field input{width:100%;height:48px;border:1px solid #dcdde1;border-radius:5px;background:#fff;padding:0 13px;font:inherit}.sbk-field input:focus{border-color:#d71920;outline:none;box-shadow:0 0 0 3px rgba(215,25,32,.09)}.sbk-form-divider{grid-column:1/-1;margin:6px 0 0;padding-top:20px;border-top:1px solid #e7e8eb}.sbk-form-divider h3{margin:0;font-size:20px}.sbk-submit{width:max-content;min-height:50px;border:0;border-radius:6px;background:#d71920;color:#fff;padding:0 26px;font-size:12px;font-weight:900;text-transform:uppercase;box-shadow:0 14px 26px rgba(215,25,32,.22);cursor:pointer}.sbk-orders{display:grid;gap:16px;padding:28px}.sbk-order{border:1px solid #e7e8eb;border-radius:8px;overflow:hidden}.sbk-order-top{display:grid;grid-template-columns:1fr auto;gap:14px;align-items:center;padding:16px 18px;background:#f7f7f8}.sbk-order-top h3{margin:0;font-size:18px}.sbk-order-top p{margin:4px 0 0;color:#666;font-size:13px}.sbk-order-total{text-align:right;font-weight:900}.sbk-order-items{display:grid;gap:14px;padding:16px 18px}.sbk-order-item{display:grid;grid-template-columns:128px minmax(0,1fr) auto;gap:16px;align-items:start;padding:14px;border:1px solid #eceef2;border-radius:8px;background:#fff}.sbk-order-item img{width:128px;height:92px;object-fit:cover;border-radius:7px;background:#f3f3f3}.sbk-order-item h4{margin:0 0 6px;font-size:16px;line-height:1.25}.sbk-order-item p{margin:0;color:#666;font-size:13px}.sbk-order-price{text-align:right;font-size:15px}.sbk-vehicle-specs{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:12px}.sbk-vehicle-specs span{display:grid;gap:3px;min-height:48px;padding:8px 10px;border-radius:6px;background:#f7f7f8;color:#111;font-size:12px}.sbk-vehicle-specs small{color:#777;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.5px}.sbk-empty{padding:28px;color:#666;line-height:1.7}@media(max-width:920px){.sbk-profile-tabs{grid-template-columns:1fr}.sbk-profile-card{text-align:left;display:grid;grid-template-columns:auto 1fr;gap:18px;align-items:center}.sbk-profile-nav{grid-column:1/-1}.sbk-vehicle-specs{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.sbk-profile .container{width:calc(100% - 28px)}.sbk-profile-body{padding:34px 0 70px}.sbk-form{grid-template-columns:1fr;padding:24px}.sbk-panel-head h2{font-size:24px}.sbk-profile-card{display:block;text-align:center;padding:24px}.sbk-submit{width:100%}.sbk-order-top{grid-template-columns:1fr}.sbk-order-total{text-align:left}.sbk-order-item{grid-template-columns:1fr}.sbk-order-item img{width:100%;height:180px}.sbk-order-price{text-align:left}.sbk-vehicle-specs{grid-template-columns:1fr 1fr}}
</style>

<section class="sbk-profile">
    <div class="sbk-profile-hero">
        <div class="container">
            <h1>My Profile</h1>
            <p>Manage your profile details and review every SBK vehicle order from one place.</p>
        </div>
    </div>

    <div class="container sbk-profile-body">
        @if(session('success'))
            <div class="sbk-alert sbk-alert--success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="sbk-alert sbk-alert--error">Please check the profile fields and try again.</div>
        @endif

        <input class="sbk-tab-radio sbk-tab-edit" type="radio" name="profile_tab" id="profileTabEdit" {{ request('tab') === 'orders' ? '' : 'checked' }}>
        <input class="sbk-tab-radio sbk-tab-orders" type="radio" name="profile_tab" id="profileTabOrders" {{ request('tab') === 'orders' ? 'checked' : '' }}>

        <div class="sbk-profile-tabs">
            <aside class="sbk-panel">
                <div class="sbk-panel-head">
                    <span>Customer</span>
                    <h2>Account</h2>
                </div>
                <div class="sbk-profile-card">
                    <img class="sbk-avatar" src="{{ $user->profile_picture_url }}" alt="{{ $user->name ?: $user->email }}">
                    <div>
                        <h3>{{ $user->name ?: 'SBK Customer' }}</h3>
                        <p>{{ $user->email }}</p>
                        <p>{{ $user->phone ?: 'No phone added' }}</p>
                        <p>{{ $user->country ?: 'No country added' }}</p>
                    </div>
                    <div class="sbk-profile-nav">
                        <label for="profileTabEdit">Edit Profile</label>
                        <label for="profileTabOrders">Orders</label>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="sbk-logout" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            <div>
                <div class="sbk-content-panel sbk-content-edit sbk-panel">
                    <div class="sbk-panel-head">
                        <span>Profile Details</span>
                        <h2>Edit profile</h2>
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
                        <div class="sbk-form-divider">
                            <h3>Change Password</h3>
                        </div>
                        <div class="sbk-field sbk-field--full">
                            <label for="current_password">Current Password</label>
                            <input id="current_password" name="current_password" type="password" required>
                        </div>
                        <div class="sbk-field">
                            <label for="password">New Password</label>
                            <input id="password" name="password" type="password" required>
                        </div>
                        <div class="sbk-field">
                            <label for="password_confirmation">Confirm Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required>
                        </div>
                        <div class="sbk-field sbk-field--full">
                            <button class="sbk-submit" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>

                <div class="sbk-content-panel sbk-content-orders sbk-panel">
                    <div class="sbk-panel-head">
                        <span>Order Record</span>
                        <h2>Vehicle orders</h2>
                    </div>
                    @if($orders->isEmpty())
                        <div class="sbk-empty">No order record is available yet.</div>
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
            </div>
        </div>
    </div>
</section>
@endsection
