@extends('front.layouts.app')

@section('title', 'SBK Official Partners | Trusted Vehicle Export Network')
@section('meta_description', 'Official partners and memberships of SBK Global Auto Trading for trusted vehicle sourcing, verification, export documentation, and buyer support.')

@php
    $assetBase = $assetBase ?? asset('assets/front');
    $partners = [
        [
            'name' => 'TVC Partner Network',
            'type' => 'Official Partner',
            'image' => 'member-tvc.webp',
            'summary' => 'Partner-backed vehicle trading access for dependable stock discovery, purchase coordination, and export support.',
            'points' => ['Vehicle sourcing support', 'Export workflow coordination', 'Buyer confidence'],
        ],
        [
            'name' => 'Japanese Car Trade',
            'type' => 'Member Network',
            'image' => 'member-jct.webp',
            'summary' => 'A trusted Japanese vehicle trade network that supports transparent sourcing and documentation guidance.',
            'points' => ['Japanese market access', 'Trade verification', 'Documentation guidance'],
        ],
        [
            'name' => 'Aisha Trading LLC',
            'type' => 'Trading Partner',
            'image' => 'member-at.webp',
            'summary' => 'Regional trading collaboration that strengthens SBK service coverage for individual and wholesale buyers.',
            'points' => ['Regional trade support', 'Client coordination', 'Global buyer service'],
        ],
    ];
    $brandLogos = ['brand-toyota.webp', 'brand-honda.webp', 'brand-nissan.webp', 'brand-mazda.webp', 'brand-bmw.webp', 'brand-mercedes.webp', 'brand-suzuki.webp', 'brand-mitsubishi.webp'];
@endphp

<style>
.sbk-partners{--red:#d71920;--red-dark:#a90f15;--ink:#111216;--muted:#68707b;--line:#e5e7eb;--soft:#f5f6f8;background:#fff;color:var(--ink);overflow:hidden}.sbk-partners *{box-sizing:border-box}.sbk-partners .container{width:min(1180px,calc(100% - 38px));max-width:none!important;margin:0 auto}.sbk-partners-hero{position:relative;isolation:isolate;background:#07080b;color:#fff}.sbk-partners-hero:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.96),rgba(0,0,0,.72) 44%,rgba(0,0,0,.24)),url("{{ $assetBase }}/img/about-building.webp") center/cover no-repeat}.sbk-partners-hero:after{content:"PARTNERS";position:absolute;right:-34px;bottom:-42px;z-index:-1;color:rgba(255,255,255,.055);font:900 clamp(76px,15vw,190px)/1 Arial,sans-serif;letter-spacing:0}.sbk-partners-hero__inner{min-height:560px;display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:42px;align-items:center;padding:76px 0 104px}.sbk-kicker{display:inline-flex;align-items:center;gap:12px;color:#ff4c54;font-size:12px;font-weight:900;letter-spacing:2px;text-transform:uppercase}.sbk-kicker:before{content:"";width:42px;height:3px;background:var(--red)}.sbk-partners-hero h1{max-width:790px;margin:16px 0 0;color:#fff;font-size:clamp(44px,7vw,82px);line-height:.94;font-weight:900}.sbk-partners-hero p{max-width:660px;margin:22px 0 0;color:rgba(255,255,255,.82);font-size:16px;line-height:1.78}.sbk-hero-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}.sbk-btn{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:0 18px;border-radius:4px;font-size:12px;font-weight:900;letter-spacing:.4px;text-transform:uppercase}.sbk-btn--red{background:var(--red);color:#fff}.sbk-btn--red:hover{background:var(--red-dark);color:#fff}.sbk-btn--light{border:1px solid rgba(255,255,255,.3);color:#fff}.sbk-btn--light:hover{border-color:#fff;color:#fff}.sbk-hero-panel{position:relative;overflow:hidden;border:1px solid rgba(255,255,255,.18);border-radius:10px;background:rgba(255,255,255,.08);box-shadow:0 28px 80px rgba(0,0,0,.38);backdrop-filter:blur(14px)}.sbk-hero-panel__top{padding:24px;border-bottom:1px solid rgba(255,255,255,.16)}.sbk-hero-panel__top span{display:block;color:#ff646b;font-size:11px;font-weight:900;letter-spacing:1.5px;text-transform:uppercase}.sbk-hero-panel__top strong{display:block;margin-top:8px;color:#fff;font-size:28px;line-height:1.08}.sbk-proof-list{display:grid}.sbk-proof-list div{display:grid;grid-template-columns:42px 1fr;gap:14px;padding:18px 24px;border-bottom:1px solid rgba(255,255,255,.12)}.sbk-proof-list div:last-child{border-bottom:0}.sbk-proof-list i{display:grid;place-items:center;width:42px;height:42px;border-radius:50%;background:#fff;color:var(--red);font-style:normal;font-weight:900}.sbk-proof-list b{display:block;color:#fff;font-size:14px}.sbk-proof-list small{display:block;margin-top:3px;color:rgba(255,255,255,.68);line-height:1.45}.sbk-partners-intro{position:relative;z-index:2;margin-top:-44px}.sbk-intro-strip{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 24px 70px rgba(17,18,22,.14);overflow:hidden}.sbk-intro-strip article{padding:24px;border-right:1px solid var(--line)}.sbk-intro-strip article:last-child{border-right:0}.sbk-intro-strip span{display:block;color:var(--red);font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:1.3px}.sbk-intro-strip strong{display:block;margin-top:9px;font-size:22px;line-height:1.1}.sbk-intro-strip p{margin:9px 0 0;color:var(--muted);font-size:13px;line-height:1.6}.sbk-partners-main{padding:82px 0 98px;background:linear-gradient(180deg,#fff 0,#f7f8fa 100%)}.sbk-section-head{display:flex;justify-content:space-between;gap:30px;align-items:flex-end;margin-bottom:30px}.sbk-section-head h2{max-width:540px;margin:0;font-size:clamp(34px,4vw,52px);line-height:1.02;font-weight:900}.sbk-section-head p{max-width:520px;margin:0;color:var(--muted);line-height:1.72}.sbk-partner-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}.sbk-partner-card{position:relative;overflow:hidden;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 18px 48px rgba(17,18,22,.08);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}.sbk-partner-card:hover{transform:translateY(-6px);border-color:#ffd0d0;box-shadow:0 28px 68px rgba(17,18,22,.14)}.sbk-partner-card__logo{display:grid;place-items:center;height:146px;margin:22px 22px 0;border:1px solid #edf0f3;border-radius:8px;background:#fff}.sbk-partner-card__logo img{display:block;max-width:210px;max-height:86px;object-fit:contain}.sbk-partner-card__body{padding:24px}.sbk-partner-card small{display:block;color:var(--red);font-size:11px;font-weight:900;letter-spacing:1.2px;text-transform:uppercase}.sbk-partner-card h3{margin:8px 0 12px;font-size:25px;line-height:1.08}.sbk-partner-card p{margin:0;color:var(--muted);font-size:14px;line-height:1.72}.sbk-partner-points{display:grid;gap:9px;margin:20px 0 0;padding:0;list-style:none}.sbk-partner-points li{display:grid;grid-template-columns:18px 1fr;gap:8px;align-items:start;color:#343941;font-size:13px;font-weight:800}.sbk-partner-points li:before{content:"";width:8px;height:8px;margin-top:5px;border-radius:50%;background:var(--red)}.sbk-network{padding:86px 0;background:#111216;color:#fff}.sbk-network-grid{display:grid;grid-template-columns:360px minmax(0,1fr);gap:44px;align-items:center}.sbk-network h2{margin:0;color:#fff;font-size:clamp(32px,4vw,48px);line-height:1.05}.sbk-network p{margin:16px 0 0;color:rgba(255,255,255,.72);line-height:1.72}.sbk-brand-wall{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.sbk-brand-wall div{display:grid;place-items:center;min-height:92px;border:1px solid rgba(255,255,255,.12);border-radius:8px;background:rgba(255,255,255,.06)}.sbk-brand-wall img{max-width:112px;max-height:46px;object-fit:contain;filter:brightness(0) invert(1);opacity:.86}.sbk-flow{padding:88px 0 98px;background:#fff}.sbk-flow-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}.sbk-flow-step{position:relative;min-height:230px;padding:24px;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 16px 42px rgba(17,18,22,.06)}.sbk-flow-step span{display:grid;place-items:center;width:46px;height:46px;border-radius:50%;background:#111;color:#fff;font-weight:900}.sbk-flow-step h3{margin:22px 0 10px;font-size:21px;line-height:1.12}.sbk-flow-step p{margin:0;color:var(--muted);font-size:14px;line-height:1.65}.sbk-cta{position:relative;isolation:isolate;overflow:hidden;background:#07080a;color:#fff}.sbk-cta:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.92),rgba(0,0,0,.63)),url("{{ $assetBase }}/img/hero-hilux.webp") center/cover no-repeat}.sbk-cta__inner{display:flex;justify-content:space-between;gap:28px;align-items:center;min-height:260px;padding:52px 0}.sbk-cta h2{max-width:650px;margin:0;color:#fff;font-size:clamp(30px,4vw,48px);line-height:1.04}.sbk-cta p{max-width:560px;margin:15px 0 0;color:rgba(255,255,255,.76);line-height:1.7}@media(max-width:980px){.sbk-partners-hero__inner,.sbk-network-grid{grid-template-columns:1fr}.sbk-hero-panel{max-width:520px}.sbk-intro-strip,.sbk-partner-grid,.sbk-flow-grid{grid-template-columns:1fr 1fr}.sbk-brand-wall{grid-template-columns:repeat(4,1fr)}}@media(max-width:720px){.sbk-partners .container{width:calc(100% - 28px)}.sbk-partners-hero__inner{min-height:0;padding:58px 0 86px}.sbk-section-head,.sbk-cta__inner{display:block}.sbk-section-head p{margin-top:14px}.sbk-intro-strip,.sbk-partner-grid,.sbk-flow-grid{grid-template-columns:1fr}.sbk-intro-strip article{border-right:0;border-bottom:1px solid var(--line)}.sbk-intro-strip article:last-child{border-bottom:0}.sbk-brand-wall{grid-template-columns:1fr 1fr}.sbk-partners-main,.sbk-flow{padding:62px 0 74px}.sbk-network{padding:64px 0}.sbk-cta .sbk-btn{margin-top:22px}}
</style>
<style>
.sbk-global-partners{position:relative;isolation:isolate;padding:76px 0 92px;background:#f8f9fb;overflow:hidden}.sbk-global-partners:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(180deg,rgba(255,255,255,.92),rgba(255,255,255,.84)),url("{{ $assetBase }}/img/hero-mercedes.webp") center/cover no-repeat;filter:grayscale(1);opacity:.82}.sbk-global-partners:after{content:"NETWORK";position:absolute;left:50%;top:18px;z-index:-1;transform:translateX(-50%);color:rgba(17,18,22,.045);font:900 clamp(76px,14vw,180px)/1 Arial,sans-serif;letter-spacing:0}.sbk-global-head{text-align:center;margin:0 auto 32px;max-width:1100px}.sbk-global-head h2{margin:0;color:#7f0508;font-size:clamp(30px,4vw,42px);line-height:1.08;font-weight:900}.sbk-global-head p{max-width:1050px;margin:14px auto 0;color:#111216;font-size:15px;line-height:1.65}.sbk-global-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:90px;align-items:center}.sbk-global-card{position:relative;display:grid;place-items:center;min-height:150px;border-radius:6px;background:#fff;border:1px solid rgba(17,18,22,.04);box-shadow:0 18px 35px rgba(17,18,22,.12);overflow:hidden}.sbk-global-card--large{min-height:225px}.sbk-global-logo{display:grid;place-items:center;text-align:center;padding:24px}.sbk-global-logo img{display:block;max-width:min(250px,100%);max-height:105px;object-fit:contain}.sbk-global-card--large .sbk-global-logo img{max-width:min(310px,100%);max-height:150px}.sbk-global-card small{position:absolute;left:18px;bottom:14px;color:#8a8f98;font-size:10px;font-weight:900;letter-spacing:1.2px;text-transform:uppercase}@media(max-width:980px){.sbk-global-grid{gap:22px}}@media(max-width:720px){.sbk-global-partners{padding:58px 0 70px}.sbk-global-grid{grid-template-columns:1fr;gap:18px}.sbk-global-card,.sbk-global-card--large{min-height:150px}.sbk-global-head{text-align:left}.sbk-global-head p{margin-left:0}}
</style>

@section('content')
<main class="sbk-partners">
    <section class="sbk-partners-hero">
        <div class="container sbk-partners-hero__inner">
            <div>
                <span class="sbk-kicker">We are the Member of</span>
                <h1>Official Partners</h1>
                <p>SBK Auto Trading works with trusted automotive networks and trading partners to make global vehicle buying more transparent, verified, and shipment-ready.</p>
                <div class="sbk-hero-actions">
                    <a href="#partner-network" class="sbk-btn sbk-btn--red">View Partners</a>
                    <a href="{{ route('contact-us') }}" class="sbk-btn sbk-btn--light">Verify With SBK</a>
                </div>
            </div>
            <aside class="sbk-hero-panel">
                <div class="sbk-hero-panel__top">
                    <span>SBK Partner Confidence</span>
                    <strong>Connected sourcing for serious buyers.</strong>
                </div>
                <div class="sbk-proof-list">
                    <div><i>01</i><p><b>Trusted trade access</b><small>Memberships that support stock discovery and coordination.</small></p></div>
                    <div><i>02</i><p><b>Document-ready support</b><small>Guidance for purchase, export, and buyer verification.</small></p></div>
                    <div><i>03</i><p><b>Worldwide customer care</b><small>SBK offices and partner coverage for multiple markets.</small></p></div>
                </div>
            </aside>
        </div>
    </section>

    <section class="sbk-partners-intro">
        <div class="container">
            <div class="sbk-intro-strip">
                <article><span>Sourcing</span><strong>Better market access</strong><p>Partners help SBK locate reliable vehicle opportunities across key markets.</p></article>
                <article><span>Verification</span><strong>Cleaner buyer decisions</strong><p>Membership-backed processes give customers stronger confidence before purchase.</p></article>
                <article><span>Export</span><strong>Smoother handover</strong><p>Our team coordinates next steps from documents to shipment readiness.</p></article>
            </div>
        </div>
    </section>

    <section class="sbk-partners-main" id="partner-network">
        <div class="container">
            <div class="sbk-section-head">
                <h2>Recognized partner network.</h2>
                <p>The official partner network supports SBK with dependable sourcing, clearer verification, and a more organized export buying experience.</p>
            </div>
            <div class="sbk-partner-grid">
                @foreach($partners as $partner)
                    <article class="sbk-partner-card">
                        <div class="sbk-partner-card__logo">
                            <img src="{{ $assetBase }}/img/{{ $partner['image'] }}" alt="{{ $partner['name'] }}">
                        </div>
                        <div class="sbk-partner-card__body">
                            <small>{{ $partner['type'] }}</small>
                            <h3>{{ $partner['name'] }}</h3>
                            <p>{{ $partner['summary'] }}</p>
                            <!-- <ul class="sbk-partner-points">
                                @foreach($partner['points'] as $point)
                                    <li>{{ $point }}</li>
                                @endforeach
                            </ul> -->
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sbk-global-partners">
        <div class="container">
            <div class="sbk-global-head">
                <h2>Partners Behind Our Global Network</h2>
                <p>Our official partners extend SBK's sourcing and trading network across key automotive markets. By working with trusted companies, we can offer customers more vehicle options, stronger market access, and better support throughout the sourcing and export process.</p>
            </div>
            <div class="sbk-global-grid">
                <article class="sbk-global-card sbk-global-card--large">
                    <div class="sbk-global-logo">
                        <img src="{{ $assetBase }}/img/partner-jdm-logo.webp" alt="JDM Trading Co. Ltd">
                    </div>
                    <small>Japan Market Partner</small>
                </article>
                <article class="sbk-global-card">
                    <div class="sbk-global-logo">
                        <img src="{{ $assetBase }}/img/partner-nazli-auto.webp" alt="Nazli Auto">
                    </div>
                    <small>Trading Network</small>
                </article>
                <article class="sbk-global-card">
                    <div class="sbk-global-logo">
                        <img src="{{ $assetBase }}/img/partner-vasia.webp" alt="V Asia">
                    </div>
                    <small>Asia Export Partner</small>
                </article>
            </div>
        </div>
    </section>

    <!-- <section class="sbk-network">
        <div class="container sbk-network-grid">
            <div>
                <span class="sbk-kicker">Brand Coverage</span>
                <h2>Built around the vehicles buyers ask for.</h2>
                <p>SBK combines partner access with brand-focused sourcing experience across Japanese, European, and global vehicle segments.</p>
            </div>
            <div class="sbk-brand-wall">
                @foreach($brandLogos as $logo)
                    <div><img src="{{ $assetBase }}/img/brand/{{ $logo }}" alt="Vehicle brand"></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="sbk-flow">
        <div class="container">
            <div class="sbk-section-head">
                <h2>How partner-backed buying works.</h2>
                <p>From the first inquiry to shipment preparation, SBK keeps the process organized, practical, and easy to follow.</p>
            </div>
            <div class="sbk-flow-grid">
                <article class="sbk-flow-step"><span>1</span><h3>Share your requirement</h3><p>Tell us the vehicle type, budget, destination, and preferred condition.</p></article>
                <article class="sbk-flow-step"><span>2</span><h3>Source through network</h3><p>SBK checks available stock and partner-backed options for your market.</p></article>
                <article class="sbk-flow-step"><span>3</span><h3>Verify before purchase</h3><p>We guide you through details, inspection notes, documents, and payment steps.</p></article>
                <article class="sbk-flow-step"><span>4</span><h3>Prepare for export</h3><p>Our team coordinates documentation and shipment readiness for the next stage.</p></article>
            </div>
        </div>
    </section> -->

    <section class="sbk-cta">
        <div class="container sbk-cta__inner">
            <div>
                <h2>Need partner verification or buying guidance?</h2>
                <p>Talk to SBK before you commit. We can explain partner-backed sourcing, stock details, export documents, and the next step for your destination market.</p>
            </div>
            <a href="{{ route('contact-us') }}" class="sbk-btn sbk-btn--red">Contact SBK</a>
        </div>
    </section>
</main>
@endsection
