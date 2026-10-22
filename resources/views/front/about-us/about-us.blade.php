@extends('front.layouts.app')
<style>
/* SBK About Us — premium editorial redesign. Scoped to this section only. */
.sbk-about-section{--red:#d71920;--red2:#a90e15;--ink:#101114;--soft:#f5f6f8;--line:#e8e8eb;position:relative;overflow:hidden;padding:105px 0 120px;background:#fff;color:var(--ink)}
.sbk-about-section *{box-sizing:border-box}.sbk-about-section>.container{width:min(1280px,calc(100% - 56px));max-width:none!important;margin:auto}.sbk-about-bg-text{position:absolute;right:-65px;top:-75px;font:900 390px/1 Arial,sans-serif;letter-spacing:-35px;color:rgba(215,25,32,.025);pointer-events:none}.sbk-about-section:before{content:"";position:absolute;left:-180px;top:360px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(215,25,32,.07),transparent 68%);pointer-events:none}
.sbk-about-heading{position:relative;z-index:2;text-align:center;margin-bottom:78px}.sbk-about-heading>span,.sbk-about-label,.sbk-ceo-small,.sbk-about-cta-content>span{font-weight:800;text-transform:uppercase;letter-spacing:2.5px}.sbk-about-heading>span{display:block;color:var(--red);font-size:12px;margin-bottom:12px}.sbk-about-heading h2{margin:0;font-size:clamp(46px,5vw,68px);font-weight:900;line-height:1;letter-spacing:-2.5px}.sbk-about-heading-line{width:78px;height:4px;margin:20px auto 0;border-radius:99px;background:linear-gradient(90deg,var(--red),#ff4c54)}
/* editorial split hero */
.sbk-about-intro{position:relative;z-index:2;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(430px,.92fr);gap:88px;align-items:center;margin-bottom:145px}.sbk-about-image{position:relative;height:590px;padding:30px 32px 0 0}.sbk-about-image:before{content:"";position:absolute;inset:0 34px 34px 0;border-radius:34px;background:linear-gradient(145deg,#f7f7f8,#eceef1);border:1px solid #ececef;box-shadow:0 35px 80px rgba(20,20,25,.11)}.sbk-about-image:after{content:"SBK";position:absolute;left:38px;bottom:42px;font:900 112px/1 Arial,sans-serif;letter-spacing:-8px;color:rgba(255,255,255,.72);z-index:1}.sbk-about-image>img{position:relative;z-index:2;width:100%;height:100%;object-fit:cover;object-position:center;border-radius:28px;box-shadow:0 22px 55px rgba(0,0,0,.12);transition:transform .5s ease,box-shadow .5s ease}.sbk-about-image:hover>img{transform:translate(-7px,-7px);box-shadow:0 34px 70px rgba(0,0,0,.17)}.sbk-about-experience{position:absolute;z-index:5;right:0;bottom:0;display:flex;align-items:center;gap:16px;min-width:220px;padding:23px 27px;color:#fff;background:linear-gradient(135deg,#e51b24,var(--red2));border-radius:18px 4px 18px 18px;box-shadow:0 20px 45px rgba(190,15,23,.30)}.sbk-about-experience strong{font-size:48px;line-height:1}.sbk-about-experience span{font-size:13px;font-weight:700;line-height:1.35}
.sbk-about-content{position:relative;padding:24px 0}.sbk-about-content:after{content:"";position:absolute;right:0;top:0;width:74px;height:74px;border-top:2px solid rgba(215,25,32,.22);border-right:2px solid rgba(215,25,32,.22)}.sbk-about-label{display:flex;align-items:center;gap:13px;color:var(--red);font-size:12px;margin-bottom:18px}.sbk-about-label:before{content:"";width:42px;height:3px;border-radius:4px;background:var(--red)}.sbk-about-content h3{margin:0 0 28px;font-size:clamp(40px,4.1vw,59px);line-height:1.05;letter-spacing:-2px;font-weight:900}.sbk-about-content h3 span{color:var(--red)}.sbk-about-content>p{max-width:570px;margin:0 0 17px;color:#60636a;font-size:16px;line-height:1.85}.sbk-about-signature{display:flex;align-items:center;gap:16px;width:max-content;max-width:100%;margin-top:32px;padding:17px 22px;border:1px solid var(--line);border-radius:14px;background:#fff;box-shadow:0 12px 30px rgba(0,0,0,.05)}.sbk-about-signature>span{width:5px;height:48px;border-radius:5px;background:var(--red)}.sbk-about-signature strong,.sbk-about-signature small{display:block}.sbk-about-signature strong{font-size:15px}.sbk-about-signature small{font-size:12px;color:#777;margin-top:4px}
/* CEO feature */
.sbk-ceo-wrapper{position:relative;z-index:3;max-width:1160px;margin:0 auto 115px;padding-top:36px}.sbk-book-car{position:absolute;z-index:10;left:50%;top:-38px;transform:translateX(-50%);min-width:230px;padding:18px 22px;text-align:center;background:#fff;border:1px solid rgba(215,25,32,.25);border-radius:16px;box-shadow:0 18px 45px rgba(0,0,0,.14)}.sbk-book-car:before{content:"";position:absolute;inset:-6px;border:1px solid rgba(215,25,32,.11);border-radius:20px;pointer-events:none}.sbk-book-car strong{display:block;font-size:18px;margin-bottom:9px}.sbk-book-car a{display:flex;align-items:center;justify-content:center;gap:8px;padding:11px 18px;border-radius:9px;background:#25D366;color:#fff;text-decoration:none;font-size:12px;font-weight:800;transition:.25s}.sbk-book-car a:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(37,211,102,.28)}
.sbk-ceo-card{position:relative;overflow:hidden;display:grid;grid-template-columns:455px minmax(0,1fr);align-items:end;min-height:520px;padding:0 78px 0 0;border-radius:34px;background:linear-gradient(125deg,#9e070d 0%,#d71920 56%,#ef2a34 100%);box-shadow:0 38px 90px rgba(150,8,15,.25)}.sbk-ceo-card:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 14% 18%,rgba(255,255,255,.18),transparent 25%),linear-gradient(120deg,transparent 50%,rgba(255,255,255,.06));pointer-events:none}.sbk-ceo-card:after{content:"SBK";position:absolute;left:-15px;bottom:-42px;font:900 180px/1 Arial,sans-serif;letter-spacing:-14px;color:rgba(255,255,255,.055)}.sbk-ceo-photo{position:relative;z-index:2;height:100%;display:flex;align-items:flex-end;justify-content:center;padding:20px 18px 0 32px}.sbk-ceo-photo:before{content:"";position:absolute;width:365px;height:365px;bottom:34px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.18),rgba(255,255,255,.055) 62%,transparent 63%)}.sbk-ceo-photo:after{content:"";position:absolute;left:42px;right:24px;bottom:8px;height:38px;border-radius:50%;background:rgba(50,0,0,.22);filter:blur(13px)}.sbk-ceo-photo img{position:relative;z-index:2;display:block;width:auto;max-width:420px;height:510px;object-fit:contain;object-position:center bottom;filter:drop-shadow(22px 22px 28px rgba(0,0,0,.24));transition:transform .45s ease,filter .45s ease}.sbk-ceo-card:hover .sbk-ceo-photo img{transform:translateY(-7px) scale(1.018);filter:drop-shadow(26px 28px 32px rgba(0,0,0,.28))}.sbk-ceo-message{position:relative;z-index:3;padding:105px 0 65px;color:#fff}.sbk-ceo-small{display:block;font-size:11px;opacity:.78;margin-bottom:13px}.sbk-ceo-message h3{margin:0 0 22px;color:#fff;font-size:clamp(34px,3.5vw,50px);line-height:1.07;letter-spacing:-1.4px}.sbk-ceo-message p{max-width:620px;margin:0 0 13px;color:rgba(255,255,255,.9);font-size:14px;line-height:1.8}.sbk-ceo-name{margin-top:22px;padding-left:15px;border-left:3px solid rgba(255,255,255,.75)}.sbk-ceo-name strong,.sbk-ceo-name small{display:block}.sbk-ceo-name strong{font-size:15px}.sbk-ceo-name small{font-size:11px;opacity:.72;margin-top:4px}.sbk-ceo-quote{position:absolute;right:45px;top:58px;color:rgba(255,255,255,.18);font:110px/1 Georgia,serif}
/* floating value cards */
.sbk-values{position:relative;z-index:6;display:grid;grid-template-columns:1fr 1fr;max-width:930px;margin:-54px auto 0;gap:24px}.sbk-value-card{position:relative;overflow:hidden;min-height:300px;padding:44px 44px 40px;border:1px solid #ececef;border-radius:22px;background:#fff;box-shadow:0 24px 60px rgba(16,17,20,.10);transition:transform .32s ease,box-shadow .32s ease}.sbk-value-card:hover{transform:translateY(-10px);box-shadow:0 34px 75px rgba(16,17,20,.15)}.sbk-value-card:before{content:"";position:absolute;left:0;top:0;width:6px;height:100%;background:linear-gradient(var(--red),#9f0d13)}.sbk-value-card:after{content:"";position:absolute;width:120px;height:120px;right:-55px;top:-55px;border:18px solid rgba(215,25,32,.045);border-radius:50%}.sbk-value-icon{width:62px;height:62px;display:flex;align-items:center;justify-content:center;margin-bottom:23px;border-radius:18px;background:linear-gradient(135deg,#e01a24,#a70d14);box-shadow:0 13px 27px rgba(215,25,32,.22)}.sbk-value-icon svg{width:29px;height:29px;fill:none;stroke:#fff;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.sbk-value-card h3{margin:0 0 10px;font-size:26px;letter-spacing:-.5px}.sbk-value-card>span{display:block;margin-bottom:14px;font-size:13px;font-weight:800;color:#333}.sbk-value-card p{margin:0;color:#676a70;font-size:14px;line-height:1.78}
/* closing CTA */
.sbk-about-cta{position:relative;z-index:3;display:grid;grid-template-columns:minmax(0,1fr) 390px;align-items:center;max-width:1040px;min-height:310px;margin:0 auto;overflow:hidden;border-radius:30px;background:linear-gradient(118deg,#9f080e,#d71920 65%,#ef2933);box-shadow:0 32px 78px rgba(155,8,15,.23)}.sbk-about-cta:before{content:"";position:absolute;width:330px;height:330px;right:-90px;top:-120px;border:55px solid rgba(255,255,255,.055);border-radius:50%}.sbk-about-cta:after{content:"";position:absolute;left:0;bottom:0;width:100%;height:1px;background:rgba(255,255,255,.25)}.sbk-about-cta-content{position:relative;z-index:3;padding:54px 25px 54px 58px;color:#fff}.sbk-about-cta-content>span{display:block;font-size:10px;opacity:.8;margin-bottom:10px}.sbk-about-cta-content h3{margin:0 0 15px;color:#fff;font-size:36px;line-height:1.12;letter-spacing:-.8px}.sbk-about-cta-content p{max-width:570px;margin:0 0 24px;color:rgba(255,255,255,.9);font-size:14px;line-height:1.75}.sbk-about-cta-content a{display:inline-flex;align-items:center;gap:15px;padding:14px 20px;border-radius:10px;background:#0b0b0c;color:#fff;text-decoration:none;font-size:12px;font-weight:800;box-shadow:0 12px 25px rgba(0,0,0,.18);transition:.25s}.sbk-about-cta-content a:hover{transform:translateY(-3px);box-shadow:0 17px 30px rgba(0,0,0,.25)}.sbk-about-cta-content a span{color:#ff4149;font-size:19px}.sbk-about-cta-image{position:relative;align-self:stretch;z-index:2}.sbk-about-cta-image:before{content:"";position:absolute;width:285px;height:285px;right:28px;bottom:-40px;border-radius:50%;background:rgba(255,255,255,.08)}.sbk-about-cta-image img{position:absolute;z-index:2;right:18px;bottom:0;width:370px;height:300px;object-fit:contain;object-position:center bottom;filter:drop-shadow(-12px 18px 24px rgba(0,0,0,.18));transition:transform .4s ease}.sbk-about-cta:hover .sbk-about-cta-image img{transform:translateX(-8px) scale(1.025)}
@media(max-width:1050px){.sbk-about-intro{grid-template-columns:1fr;gap:55px}.sbk-about-image{height:560px;max-width:820px;margin:auto}.sbk-about-content{max-width:820px;margin:auto}.sbk-ceo-card{grid-template-columns:360px 1fr;padding-right:42px}.sbk-ceo-photo img{height:470px;max-width:350px}.sbk-about-cta{grid-template-columns:1fr 330px}}
@media(max-width:767px){.sbk-about-section{padding:72px 0 85px}.sbk-about-section>.container{width:min(100% - 32px,1280px)}.sbk-about-heading{margin-bottom:50px}.sbk-about-intro{margin-bottom:105px}.sbk-about-image{height:430px;padding:18px 18px 0 0}.sbk-about-experience{right:0;padding:17px 20px}.sbk-about-experience strong{font-size:38px}.sbk-about-content h3{font-size:39px}.sbk-ceo-wrapper{padding-top:48px}.sbk-ceo-card{display:block;padding:72px 28px 38px}.sbk-ceo-photo{height:360px;padding:0}.sbk-ceo-photo:before{width:285px;height:285px;bottom:24px}.sbk-ceo-photo:after{left:18%;right:18%;bottom:0}.sbk-ceo-photo img{height:365px;width:auto;max-width:100%}.sbk-ceo-message{padding:28px 0 0}.sbk-values{grid-template-columns:1fr;margin-top:22px}.sbk-about-cta{grid-template-columns:1fr}.sbk-about-cta-content{padding:42px 32px}.sbk-about-cta-image{height:285px}.sbk-about-cta-image img{left:50%;right:auto;transform:translateX(-50%);height:280px}.sbk-about-cta:hover .sbk-about-cta-image img{transform:translateX(-50%) scale(1.02)}}
@media(max-width:480px){.sbk-about-section>.container{width:calc(100% - 24px)}.sbk-about-heading h2{font-size:42px}.sbk-about-image{height:350px}.sbk-about-content h3{font-size:34px}.sbk-about-content>p{font-size:15px}.sbk-about-experience{min-width:0}.sbk-ceo-card{border-radius:22px;padding-left:20px;padding-right:20px}.sbk-ceo-message h3{font-size:31px}.sbk-value-card{padding:34px 27px}.sbk-about-cta{border-radius:22px}.sbk-about-cta-content{padding:34px 24px}.sbk-about-cta-content h3{font-size:29px}}
@media(prefers-reduced-motion:reduce){.sbk-about-section *{transition:none!important}}
</style>


@section('content')
@php
$assetBase = asset('assets/front');
@endphp
<!-- =========================
     ABOUT US SECTION
========================= -->
<section class="sbk-about-section" id="about-us">
    <div class="sbk-about-bg-text">SBK</div>

    <div class="container">

        <!-- Section Heading -->
        <div class="sbk-about-heading">
            <span>ABOUT SBK</span>
            <h2>About Us</h2>
            <div class="sbk-about-heading-line"></div>
        </div>

        <!-- WHO WE ARE -->
        <div class="sbk-about-intro">

            <div class="sbk-about-image">
                <!-- Replace with your About Us image -->
                <img
                    src="{{ $assetBase }}/img/about-building.webp"
                    alt="SBK Global Auto Trading"
                >

                <div class="sbk-about-experience">
                    <strong>15+</strong>
                    <span>Years of<br>Experience</span>
                </div>
            </div>

            <div class="sbk-about-content">
                <span class="sbk-about-label">WHO WE ARE</span>

                <h3>Global Auto Trading,<br>
                    <span>Built on Experience</span>
                </h3>

                <p>
                    SBK Auto Trading is an international vehicle importer and
                    export company with 15 years of experience in the automotive
                    trading industry.
                </p>

                <p>
                    We focus on providing quality vehicles, transparent service
                    and professional support to customers worldwide. Our
                    experienced team helps customers through every stage of
                    their vehicle purchase, from selection to export and
                    shipment.
                </p>

                <p>
                    Whether you're looking for a reliable family vehicle,
                    commercial vehicle or your next dream car, our goal is to
                    make the buying process simple, secure and dependable.
                </p>

                <div class="sbk-about-signature">
                    <span></span>
                    <div>
                        <strong>SBK Global Auto Trading</strong>
                        <small>Premium Vehicles Worldwide</small>
                    </div>
                </div>
            </div>

        </div>


        <!-- CEO MESSAGE -->
        <div class="sbk-ceo-wrapper">

            <!-- Floating WhatsApp box -->
            <div class="sbk-book-car">
                <strong>Book a Car!</strong>

                <a href="https://wa.me/817094275106"
                   target="_blank"
                   rel="noopener">
                    <span>●</span>
                    WhatsApp
                </a>
            </div>


            <div class="sbk-ceo-card">

                <div class="sbk-ceo-photo">
                    <!-- Replace with CEO image -->
                    <img
                        src="{{ $assetBase }}/img/about-owner-portrait.png"
                        alt="SBK CEO"
                    >
                </div>

                <div class="sbk-ceo-message">
                    <span class="sbk-ceo-small">MESSAGE FROM THE CEO</span>

                    <h3>Driven by Trust.<br>
                        <span>Powered by Experience.</span>
                    </h3>

                    <p>
                        Finding reliable vehicles internationally often feels
                        uncertain. Unclear pricing, hidden issues and weak
                        support can make the process difficult.
                    </p>

                    <p>
                        At SBK Global Auto Trading, our focus is on transparency,
                        dependable customer service and making international
                        vehicle purchasing as simple as possible.
                    </p>

                    <div class="sbk-ceo-name">
                        <strong>SBK Auto Trading</strong>
                        <small>Global Automotive Trading</small>
                    </div>
                </div>

                <div class="sbk-ceo-quote">”</div>

            </div>


            <!-- Vision & Mission -->
            <div class="sbk-values">

                <article class="sbk-value-card">
                    <div class="sbk-value-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>

                    <h3>Our Vision</h3>

                    <span>Raising the Standard of Global Auto Trading</span>

                    <p>
                        To build a globally recognized automotive trading brand
                        by delivering consistent quality, transparent processes,
                        and dependable service across international markets.
                    </p>
                </article>


                <article class="sbk-value-card">
                    <div class="sbk-value-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <circle cx="12" cy="12" r="5"/>
                            <circle cx="12" cy="12" r="2"/>
                            <path d="M15 9l5-5M17 4h3v3"/>
                        </svg>
                    </div>

                    <h3>Our Mission</h3>

                    <span>Reliable Sourcing. Worldwide Access.</span>

                    <p>
                        To provide clients with access to quality vehicles
                        through expert sourcing, clear processes, and exceptional
                        global logistics, ensuring every transaction is
                        professional and dependable.
                    </p>
                </article>

            </div>

        </div>


        <!-- BUYERS CTA -->
        <div class="sbk-about-cta">

            <div class="sbk-about-cta-content">
                <span>GLOBAL VEHICLE SOURCING</span>

                <h3>For Buyers and Trade Partners</h3>

                <p>
                    Individual buyers and trade partners can work with SBK to
                    find vehicles matching their requirements, whether sourcing
                    one vehicle or managing bulk purchases.
                </p>

                <a href="#">
                    Get Vehicles With Our Team
                    <span>→</span>
                </a>
            </div>

            <div class="sbk-about-cta-image">
                <!-- Replace with your sales/support person image -->
                <img
                    src="{{ $assetBase }}/img/about-agent.webp"
                    alt="SBK Vehicle Support"
                >
            </div>

        </div>

    </div>
</section>
@include('front.partials.about-services')
@include('front.partials.about-contact')
@endsection


