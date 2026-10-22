
@php
$assetBase = asset('assets/front');
$serviceAsset = $assetBase . '/img/services';
$services = [
    [
        'title' => 'Japanese Vehicle Sourcing',
        'text' => 'We source vehicles directly from Japanese auctions and trusted dealer networks. You get access to verified listings, clear specifications, and real market value. We shortlist options based on your budget and destination requirements, so you review serious choices, not random inventory.',
        'button' => 'View Stock',
        'image' => 'services-sourcing.webp',
        'bg' => 'services-dark-bg.webp',
        'dark' => true,
        'reverse' => false,
    ],
    [
        'title' => 'Auction Bidding and Consultation',
        'text' => 'Japanese auctions can be complex. We simplify them. Our team explains auction grades, reviews condition reports, and recommends practical bid ranges. You stay informed at every step and make decisions with clarity, not pressure.',
        'button' => 'Start Consultation',
        'image' => 'services-dealership.webp',
        'bg' => 'services-light-bg.webp',
        'dark' => false,
        'reverse' => true,
    ],
    [
        'title' => 'Vehicle Inspection and Condition Verification',
        'text' => 'Before any commitment, we carefully review the vehicle\'s condition. That includes auction sheet translation, mileage verification, and accident history checks. You receive a clear understanding of what you\'re buying before it moves forward.',
        'button' => 'Verify Vehicle',
        'image' => 'services-customs.webp',
        'bg' => 'services-inspection-bg.webp',
        'dark' => true,
        'reverse' => false,
    ],
    [
        'title' => 'Shipping and Logistics Management',
        'text' => 'We manage shipping from Japan to your destination port with structured coordination. Freight booking, export clearance, and shipment tracking are handled professionally. You receive timely updates while your vehicle moves through each stage.',
        'button' => 'Track Shipment',
        'image' => 'services-logistics.webp',
        'bg' => 'services-doc-bg.webp',
        'dark' => false,
        'reverse' => true,
    ],
    [
        'title' => 'Documentation and Import Assistance',
        'text' => 'Before any commitment, we carefully review the vehicle\'s condition. That includes auction sheet translation, mileage verification, and accident history checks. You receive a clear understanding of what you\'re buying before it moves forward.',
        'button' => 'Get Support',
        'image' => 'services-parts.webp',
        'bg' => 'services-parts-bg.png',
        'dark' => true,
        'reverse' => false,
    ],
];
@endphp


<style>
.sbk-services{--red:#a40505;--bright-red:#d20a26;--ink:#111;background:#fff;color:var(--ink);overflow:hidden}.sbk-services *{box-sizing:border-box}.sbk-services .container{width:min(1200px,calc(100% - 36px));max-width:none!important;margin:0 auto}.sbk-services-hero{position:relative;display:grid;place-items:center;min-height:282px;background:#111;color:#fff;isolation:isolate}.sbk-services-hero:before{content:"";position:absolute;inset:0;background:url("{{ $serviceAsset }}/slide-sbk.webp") center/cover no-repeat;opacity:.68;z-index:-2}.sbk-services-hero:after{content:"";position:absolute;inset:0;background:rgba(0,0,0,.36);z-index:-1}.sbk-services-hero h1{margin:0;font-size:76px;line-height:1;font-weight:600;background-image:linear-gradient(179deg,#e6e6e6,#d2a3a3);-webkit-background-clip:text;background-clip:text;color:transparent}.sbk-services-intro{padding:52px 0 50px;text-align:center}.sbk-services-intro h2{max-width:780px;margin:0 auto 26px;color:#a40505;font-size:40px;font-weight:800;line-height:42px}.sbk-services-intro p{max-width:760px;margin:0 auto;color:#000;font-size:15px;line-height:22px}.sbk-service-band{position:relative;padding:30px 0;isolation:isolate}.sbk-service-band:before{content:"";position:absolute;inset:0;background-image:var(--service-bg);background-position:center;background-repeat:no-repeat;background-size:cover;opacity:.14;z-index:-1}.sbk-service-band--light{background:#fff;color:#000}.sbk-service-band--light:before{opacity:.12}.sbk-service-band--dark{background:#000;color:#fff}.sbk-service-band--dark:before{opacity:.16}.sbk-service-grid{display:grid;grid-template-columns:40% 60%;align-items:center;gap:0}.sbk-service-band--reverse .sbk-service-grid{grid-template-columns:52% 48%}.sbk-service-band--reverse .sbk-service-media{order:2}.sbk-service-band--reverse .sbk-service-copy{order:1}.sbk-service-media{padding:0 10px}.sbk-service-media img{display:block;width:85%;max-height:330px;object-fit:contain;border-radius:6px}.sbk-service-band--reverse .sbk-service-media img{width:89%;margin-left:auto}.sbk-service-copy{padding:0 10px}.sbk-service-title-ghost{margin:20px 0 0;font-size:40px;font-weight:800;line-height:1;color:rgba(255,255,255,.18)}.sbk-service-band--light .sbk-service-title-ghost{color:rgba(0,0,0,.19)}.sbk-service-copy h2{margin:-10px 0 26px;font-size:40px;font-weight:800;line-height:1.02;color:#fff;-webkit-text-stroke:1px #0e0d0d}.sbk-service-band--light .sbk-service-copy h2{color:#a40505;-webkit-text-stroke:2px #980404}.sbk-service-copy p{width:77%;margin:0 0 24px;color:#fff;font-size:14px;line-height:1.55}.sbk-service-band--light .sbk-service-copy p{width:80%;color:#000;font-size:15px}.sbk-service-copy a{display:inline-flex;align-items:center;justify-content:center;min-height:47px;padding:0 50px;border:1px solid #b11313;border-radius:7px;background:transparent;color:inherit;font-size:15px;font-weight:500}.sbk-service-copy a:hover{background:#000;color:#fff}.sbk-service-cta-wrap{padding:42px 0 120px}.sbk-service-cta{position:relative;max-width:830px;margin:0 auto;min-height:284px;padding:44px 32px;border-radius:10px;background:#d20a26;color:#fff;overflow:hidden}.sbk-service-cta h2{max-width:552px;margin:0 0 12px;color:#fff;font-size:28px;line-height:1.2}.sbk-service-cta p{max-width:552px;margin:0 0 28px;color:#fff;font-size:14px;line-height:21px}.sbk-service-actions{display:flex;gap:30px;flex-wrap:wrap}.sbk-service-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:45px;padding:0 50px;border-radius:6px;font-size:16px;font-weight:500}.sbk-service-actions a:first-child{background:#fff;color:#000}.sbk-service-actions a:last-child{background:#000;color:#fff}.sbk-service-cta img{position:absolute;right:24px;top:8px;width:37%;max-width:300px}
@media(max-width:900px){.sbk-services-hero h1{font-size:52px}.sbk-service-grid,.sbk-service-band--reverse .sbk-service-grid{grid-template-columns:1fr}.sbk-service-band--reverse .sbk-service-media,.sbk-service-band--reverse .sbk-service-copy{order:initial}.sbk-service-media img,.sbk-service-band--reverse .sbk-service-media img{width:min(420px,100%);margin:0 auto 24px}.sbk-service-copy p,.sbk-service-band--light .sbk-service-copy p{width:100%}.sbk-service-cta img{opacity:.18}.sbk-service-cta h2,.sbk-service-cta p{max-width:100%}}
@media(max-width:520px){.sbk-services .container{width:calc(100% - 28px)}.sbk-services-hero{min-height:210px}.sbk-services-hero h1{font-size:38px}.sbk-services-intro h2{font-size:26px;line-height:1.15}.sbk-service-copy h2,.sbk-service-title-ghost{font-size:28px}.sbk-service-copy h2{margin:-4px 0 18px}.sbk-service-copy a,.sbk-service-actions a{width:100%;padding:0 22px}.sbk-service-actions{gap:14px}.sbk-service-cta{padding:30px 22px}.sbk-service-cta img{display:none}}
</style>

<section class="sbk-services">

    <div class="sbk-services-intro">
        <div class="container">
            <h2>Our Services - End-to-End<br>Vehicle Export Management</h2>
            <p>Structured sourcing. Disciplined bidding. Managed logistics. Complete documentation support. Our services bring the entire Japanese vehicle export process under one coordinated system. Each step is handled with clarity, from vehicle selection to final shipment. We work with car dealers, resellers, fleet and rental operators, import-export businesses, bulk buyers, construction companies, taxi services, and individual buyers seeking reliable access to Japan's vehicle market. When the process demands accuracy and oversight, our services provide the control needed to move forward with confidence.</p>
        </div>
    </div>

    @foreach($services as $service)
        <section class="sbk-service-band {{ $service['dark'] ? 'sbk-service-band--dark' : 'sbk-service-band--light' }} {{ $service['reverse'] ? 'sbk-service-band--reverse' : '' }}" style="--service-bg:url('{{ $serviceAsset }}/{{ $service['bg'] }}')">
            <div class="container sbk-service-grid">
                <div class="sbk-service-media">
                    <img src="{{ $serviceAsset }}/{{ $service['image'] }}" alt="{{ $service['title'] }}">
                </div>
                <div class="sbk-service-copy">
                    <div class="sbk-service-title-ghost">{{ $service['title'] }}</div>
                    <h2>{{ $service['title'] }}</h2>
                    <p>{{ $service['text'] }}</p>
                    <a href="{{ route('contact-us') }}">{{ $service['button'] }}</a>
                </div>
            </div>
        </section>
    @endforeach

    <div class="sbk-service-cta-wrap">
        <div class="container">
            <div class="sbk-service-cta">
                <h2>Let's Structure Your Next Shipment</h2>
                <p>Japanese vehicle sourcing requires clarity, timing, and the right execution partner. If you're planning your next purchase or bulk order, let's structure it properly from the start.</p>
                <div class="sbk-service-actions">
                    <a href="{{ route('contact-us') }}">Start Consultation</a>
                    <a href="{{ route('contact-us') }}">Contact Our Team</a>
                </div>
                <img src="{{ $serviceAsset }}/services-cta-car.webp" alt="">
            </div>
        </div>
    </div>
</section>

