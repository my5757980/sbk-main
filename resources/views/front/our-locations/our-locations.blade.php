@extends('front.layouts.app')

@php
$assetBase = asset('assets/front');
$locations = [
    [
        'title' => 'Headquarter UAE',
        'description' => "Manages SBK's international operations, customer enquiries, vehicle sourcing, and global trading activities from Ajman.",
        'address' => 'Amber Gem Tower, Mezzanine Floor, Sheikh Khalifa Street, PO. Box 4848, Ajman, United Arab Emirates',
        'phone' => '+971 55 417 7311',
        'map' => 'Amber Gem Tower Ajman United Arab Emirates',
    ],
    [
        'title' => 'USA Office',
        'description' => 'SBK Auto Trading USA office provides support and sourcing services for North American vehicles and clients.',
        'address' => 'Prior Lake, MN 55372, USA',
        'phone' => '+1 70-9427-5106',
        'map' => 'Prior Lake MN 55372 USA',
    ],
    [
        'title' => 'Japan Office',
        'description' => 'SBK Auto Trading Japan office handles direct Japanese auction sourcing, vehicle inspection, and worldwide export logistics.',
        'address' => 'Yokohama-Shi, Kanagawa-Ken, Japan',
        'phone' => '+81 70-9427-5106',
        'map' => 'Yokohama-Shi Kanagawa-Ken Japan',
    ],
    [
        'title' => 'Kenya Office (Showroom Mombasa)',
        'description' => 'SBK Auto Trading Kenya office is located in Mombasa, serving customers across Kenya and East Africa with ready stock vehicles.',
        'address' => 'Showroom No 5, Jdm Motors Behind Kra, Nyerere Road, Mombasa, Kenya',
        'phone' => '+254 705719918',
        'map' => 'Nyerere Road Mombasa Kenya',
    ],
    [
        'title' => 'Jamaica Office',
        'description' => 'SBK Auto Trading Jamaica office provides imported vehicle support, documentation management, and local client assistance.',
        'address' => '3 South Avenue, Kingston 5, Jamaica',
        'phone' => '+1 876 8036277 / +1 876 8772811',
        'map' => '3 South Avenue Kingston 5 Jamaica',
    ],
    [
        'title' => 'Thailand Office',
        'description' => 'SBK Auto Trading Thailand office manages vehicle logistics, export support, and regional commercial operations.',
        'address' => 'Bangkok, Thailand',
        'phone' => '+66 80-000-0000',
        'map' => 'Bangkok Thailand',
    ],
    [
        'title' => 'Pakistan Office',
        'description' => 'SBK Auto Trading Pakistan office manages administrative operations, regional trade support, and customer consultations.',
        'address' => 'SB-36, Gulshan-e-Iqbal 13-A, Karachi, Pakistan',
        'phone' => '+92 213 339-3946 / +92 3708536560',
        'map' => 'SB-36 Gulshan-e-Iqbal 13-A Karachi Pakistan',
    ],
];
@endphp

@section('content')
<style>
.sbk-locations{background:#fff;color:#111;padding:28px 0 54px}.sbk-locations *{box-sizing:border-box}.sbk-locations .container{width:min(1200px,calc(100% - 36px));max-width:none!important;margin:0 auto}.sbk-location-rate{margin:0 0 18px;color:#222;font-size:14px}.sbk-location-rate select{height:33px;min-width:138px;margin:0 6px;border:1px solid #d5d5d5;border-radius:2px;background:#fff;padding:0 8px;color:#222}.sbk-locations h1{margin:0 0 18px;text-align:center;font-size:58px;line-height:1.05;font-weight:900;color:#be0000}.sbk-location-intro{max-width:970px;margin:0 auto 58px}.sbk-location-intro h2{margin:0 0 10px;font-size:26px;line-height:1.2;color:#111}.sbk-location-intro p{margin:0;color:#111;font-size:15px;line-height:1.65}.sbk-location-list{display:grid;gap:34px}.sbk-location-card{display:grid;grid-template-columns:minmax(0,.96fr) minmax(420px,1fr);gap:38px;align-items:center;margin:0;padding:28px;border:1px solid rgba(190,0,0,.65);border-radius:11px;background:#111417;color:#fff;box-shadow:0 18px 34px rgba(0,0,0,.18)}.sbk-location-card h3{width:max-content;max-width:100%;margin:0 0 27px;padding-bottom:13px;border-bottom:3px solid #ff1b2d;font-size:24px;line-height:1.25;font-weight:800;color:#fff}.sbk-location-card p{margin:0 0 14px;color:#dfe9ff;font-size:15px;line-height:1.7}.sbk-location-card iframe{display:block;width:100%;height:326px;margin:0;border:1px solid rgba(190,0,0,.85);border-radius:8px;filter:invert(92%) hue-rotate(180deg) saturate(.8) contrast(.95)}.sbk-location-label{font-weight:800;color:#ff3344}.sbk-location-copy{min-width:0}
@media(max-width:900px){.sbk-location-card{grid-template-columns:1fr;gap:24px}.sbk-location-card iframe{height:300px}.sbk-locations h1{font-size:46px}.sbk-location-intro{margin-bottom:38px}}
@media(max-width:480px){.sbk-locations .container{width:calc(100% - 28px)}.sbk-location-rate select{display:block;margin:8px 0}.sbk-locations h1{font-size:38px;text-align:left}.sbk-location-intro h2{font-size:23px}.sbk-location-card{padding:22px}.sbk-location-card h3{font-size:22px}.sbk-location-card p{font-size:14px}.sbk-location-card iframe{height:245px}}
</style>

<section class="sbk-locations">
    <div class="container">
       

        <h1>Our Offices Locations</h1>

        <div class="sbk-location-intro">
            <h2>Worldwide</h2>
            <p>SBK Global Auto Trading operates across key automotive markets to support vehicle sourcing, export, logistics and customer enquiries. Find your nearest SBK office and connect with a team that understands your market and vehicle requirements.</p>
        </div>

        <div class="sbk-location-list">
            @foreach($locations as $location)
                <article class="sbk-location-card">
                    <div class="sbk-location-copy">
                        <h3>{{ $location['title'] }}</h3>
                        <p>{{ $location['description'] }}</p>
                        <p>📍 <span class="sbk-location-label">Address:</span> {{ $location['address'] }}</p>
                        <p>📞 <span class="sbk-location-label">Phone:</span> {{ $location['phone'] }}</p>
                        <p>🕒 <span class="sbk-location-label">Working Hours:</span> Mon - Sat: 9:00 AM - 6:00 PM | Sunday: Closed</p>
                    </div>
                    <iframe
                        src="https://maps.google.com/maps?q={{ rawurlencode($location['map']) }}&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed"
                        referrerpolicy="no-referrer-when-downgrade"
                        aria-label="Map location for {{ $location['title'] }}"></iframe>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
