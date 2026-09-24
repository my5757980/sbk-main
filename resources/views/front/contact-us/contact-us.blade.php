@extends('front.layouts.app')

@php
$assetBase = asset('assets/front');
$offices = [
    [
        'name' => 'Headquarter UAE',
        'phone' => '+971 55 417 7311',
        'address' => 'Amber Gem Tower, Mezzanine Floor, Sheikh Khalifa Street, P.O. Box 4848, Ajman, United Arab Emirates',
        'time' => 'Asia/Dubai',
    ],
    [
        'name' => 'USA Office',
        'phone' => '+1 (321) 357-857',
        'address' => 'Prior Lake, MN 55372, USA',
        'time' => 'America/Chicago',
    ],
    [
        'name' => 'Japan Office',
        'phone' => '+81 70-9427-5106',
        'address' => 'Yokohama-Shi, Kanagawa-Ken, Japan',
        'time' => 'Asia/Tokyo',
    ],
    [
        'name' => 'Showroom Mombasa',
        'phone' => '+254 705719918',
        'address' => 'Nyere Avenue Showroom No 5 Jdm Motors Behind Kra Mombasa',
        'time' => 'Africa/Nairobi',
    ],
    [
        'name' => 'Jamaica Office',
        'phone' => '+1 876 8036277',
        'address' => '3 South Avenue Kingston 5, Jamaica',
        'time' => 'America/Jamaica',
    ],
    [
        'name' => 'Pakistan Office',
        'phone' => '+92 213 339-3946',
        'address' => 'SB-36, Gulshan-e-Iqbal 13-D Karachi, Pakistan',
        'time' => 'Asia/Karachi',
    ],
];
@endphp

@section('content')
<style>
.sbk-contact{--red:#d50000;--red-dark:#a80000;--ink:#111214;--muted:#626873;--line:#e7e8eb;--soft:#f5f6f8;--black:#050505;background:#fff;color:var(--ink);overflow:hidden}.sbk-contact *{box-sizing:border-box}.sbk-contact .container{width:min(1180px,calc(100% - 36px));max-width:none!important;margin:0 auto}.sbk-contact a{color:inherit}
.sbk-contact-hero{position:relative;isolation:isolate;color:#fff;background:#050505}.sbk-contact-hero:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.94),rgba(0,0,0,.68) 42%,rgba(0,0,0,.2)),url("{{ $assetBase }}/img/hero-mercedes.webp") center right/cover no-repeat}.sbk-contact-hero:after{content:"CONTACT";position:absolute;right:-48px;bottom:-52px;z-index:-1;color:rgba(255,255,255,.055);font:900 142px/1 Arial,sans-serif;letter-spacing:-8px}.sbk-contact-hero__grid{min-height:520px;display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:48px;align-items:center;padding:70px 0 95px}.sbk-contact-kicker{display:inline-flex;align-items:center;gap:12px;margin-bottom:18px;color:#fff;font-size:12px;font-weight:900;letter-spacing:2px;text-transform:uppercase}.sbk-contact-kicker:before{content:"";width:42px;height:3px;background:var(--red)}.sbk-contact-hero h1{max-width:760px;margin:0;font-size:clamp(42px,6vw,78px);line-height:.98;font-weight:900;letter-spacing:0}.sbk-contact-hero p{max-width:625px;margin:22px 0 0;color:rgba(255,255,255,.82);font-size:16px;line-height:1.78}.sbk-contact-crumb{display:flex;gap:10px;align-items:center;margin-top:28px;color:rgba(255,255,255,.72);font-size:12px;font-weight:800;text-transform:uppercase}.sbk-contact-crumb b{color:#fff}.sbk-contact-card{border:1px solid rgba(255,255,255,.18);border-radius:8px;background:rgba(255,255,255,.08);backdrop-filter:blur(12px);padding:26px;box-shadow:0 26px 72px rgba(0,0,0,.38)}.sbk-contact-card strong{display:block;font-size:28px;line-height:1.08}.sbk-contact-card p{margin:13px 0 22px;font-size:13px;line-height:1.65;color:rgba(255,255,255,.76)}.sbk-contact-card a{display:inline-flex;align-items:center;min-height:42px;padding:0 16px;border-radius:4px;background:var(--red);color:#fff;font-size:12px;font-weight:900;text-transform:uppercase}.sbk-contact-card a:hover{background:var(--red-dark);color:#fff}
.sbk-contact-strip{position:relative;z-index:2;margin-top:-42px}.sbk-contact-strip__inner{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 18px 42px rgba(17,18,20,.12)}.sbk-contact-pill{display:flex;gap:16px;align-items:center;min-height:112px;padding:22px 24px;border-right:1px solid var(--line)}.sbk-contact-pill:last-child{border-right:0}.sbk-contact-pill i{display:grid;place-items:center;flex:0 0 46px;width:46px;height:46px;border-radius:8px;background:var(--red);color:#fff;font-style:normal;font-weight:900}.sbk-contact-pill b{display:block;font-size:15px}.sbk-contact-pill span{display:block;margin-top:5px;color:var(--muted);font-size:12px;line-height:1.45}
.sbk-contact-body{padding:76px 0 92px}.sbk-contact-layout{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:34px;align-items:start}.sbk-contact-panel{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 20px 55px rgba(17,18,20,.09)}.sbk-contact-form-head{padding:30px 32px;background:linear-gradient(135deg,#101114,#050505);color:#fff}.sbk-contact-form-head span{display:block;color:#ff4d55;font-size:11px;font-weight:900;letter-spacing:1.6px;text-transform:uppercase}.sbk-contact-form-head h2{margin:10px 0 0;color:#fff;font-size:32px;line-height:1.1}.sbk-contact-form{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:30px 32px 34px}.sbk-contact-field{display:grid;gap:8px}.sbk-contact-field--full{grid-column:1/-1}.sbk-contact-field label{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.7px}.sbk-contact-field input,.sbk-contact-field textarea{width:100%;border:1px solid #dcdde1;border-radius:4px;background:#fff;color:#111;font:inherit;outline:none;transition:border-color .2s ease,box-shadow .2s ease}.sbk-contact-field input{height:48px;padding:0 13px}.sbk-contact-field textarea{min-height:150px;resize:vertical;padding:13px}.sbk-contact-field input:focus,.sbk-contact-field textarea:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(213,0,0,.09)}.sbk-contact-submit{display:inline-flex;align-items:center;justify-content:center;width:max-content;min-height:48px;padding:0 24px;border:0;border-radius:4px;background:var(--red);color:#fff;font-size:12px;font-weight:900;text-transform:uppercase;cursor:pointer;box-shadow:0 14px 26px rgba(213,0,0,.18)}.sbk-contact-submit:hover{background:var(--red-dark)}.sbk-contact-alert{grid-column:1/-1;padding:13px 14px;border-radius:4px;font-size:13px;line-height:1.45}.sbk-contact-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}.sbk-contact-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-contact-alert ul{margin:8px 0 0;padding-left:18px}
.sbk-contact-side{display:grid;gap:18px}.sbk-contact-box{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 14px 35px rgba(17,18,20,.06)}.sbk-contact-box h3{margin:0;padding:19px 21px;border-bottom:1px solid var(--line);background:#fafafa;font-size:18px}.sbk-contact-info{display:grid;gap:14px;padding:20px}.sbk-contact-info>a,.sbk-contact-info>div{display:grid;grid-template-columns:34px minmax(0,1fr);gap:12px;align-items:start}.sbk-contact-info i{display:grid;place-items:center;width:34px;height:34px;border-radius:6px;background:#111;color:#fff;font-style:normal;font-size:13px;font-weight:900}.sbk-contact-info b{display:block;font-size:13px}.sbk-contact-info span{display:block;margin-top:3px;color:var(--muted);font-size:13px;line-height:1.5}.sbk-contact-whatsapp{padding:24px;background:linear-gradient(135deg,#d50000,#101114);color:#fff}.sbk-contact-whatsapp h3{padding:0;border:0;background:transparent;color:#fff;font-size:24px}.sbk-contact-whatsapp p{margin:10px 0 18px;color:rgba(255,255,255,.82);line-height:1.65}.sbk-contact-whatsapp a{display:inline-flex;min-height:42px;align-items:center;padding:0 16px;border-radius:4px;background:#fff;color:#111;font-size:12px;font-weight:900;text-transform:uppercase}
.sbk-contact-offices{padding:6px 0 94px;background:linear-gradient(180deg,#fff 0,#f6f7f8 100%)}.sbk-contact-heading{display:flex;justify-content:space-between;gap:26px;align-items:flex-end;margin-bottom:26px}.sbk-contact-heading h2{margin:0;font-size:34px;line-height:1.08}.sbk-contact-heading p{max-width:470px;margin:0;color:var(--muted);line-height:1.65}.sbk-office-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.sbk-office{position:relative;overflow:hidden;min-height:220px;padding:23px;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 12px 32px rgba(17,18,20,.06)}.sbk-office:before{content:"";position:absolute;left:0;top:0;width:5px;height:100%;background:var(--red)}.sbk-office small{color:var(--red);font-weight:900;text-transform:uppercase;letter-spacing:1px}.sbk-office h3{margin:10px 0 13px;font-size:21px;line-height:1.18}.sbk-office p{margin:0 0 10px;color:var(--muted);font-size:13px;line-height:1.62}.sbk-office a{font-weight:900;color:#111}.sbk-contact-map{position:relative;overflow:hidden;margin-top:22px;min-height:260px;border-radius:8px;background:#090a0c;color:#fff;display:grid;place-items:center;text-align:center;padding:34px}.sbk-contact-map:before{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(0,0,0,.92),rgba(0,0,0,.5)),url("{{ $assetBase }}/img/about-building.webp") center/cover no-repeat;opacity:.9}.sbk-contact-map div{position:relative;z-index:2}.sbk-contact-map h3{margin:0 0 10px;color:#fff;font-size:30px}.sbk-contact-map p{max-width:620px;margin:0 auto;color:rgba(255,255,255,.78);line-height:1.7}
@media(max-width:1050px){.sbk-contact-hero__grid{grid-template-columns:1fr;min-height:0}.sbk-contact-card{max-width:520px}.sbk-contact-layout{grid-template-columns:1fr}.sbk-contact-side{grid-template-columns:1fr 1fr}.sbk-contact-whatsapp{grid-column:1/-1}.sbk-office-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.sbk-contact-hero__grid{padding:58px 0 82px}.sbk-contact-strip__inner{grid-template-columns:1fr}.sbk-contact-pill{border-right:0;border-bottom:1px solid var(--line)}.sbk-contact-pill:last-child{border-bottom:0}.sbk-contact-heading{display:block}.sbk-contact-heading p{margin-top:12px}.sbk-contact-form{grid-template-columns:1fr;padding:24px}.sbk-contact-form-head{padding:26px 24px}.sbk-contact-side{grid-template-columns:1fr}.sbk-office-grid{grid-template-columns:1fr}}
@media(max-width:480px){.sbk-contact .container{width:calc(100% - 28px)}.sbk-contact-hero h1{font-size:39px}.sbk-contact-hero:after{font-size:76px;right:-24px;bottom:-24px}.sbk-contact-card{padding:22px}.sbk-contact-body{padding-top:58px}.sbk-contact-form-head h2,.sbk-contact-heading h2{font-size:28px}}
</style>

<section class="sbk-contact">
    <div class="sbk-contact-hero">
        <div class="container sbk-contact-hero__grid">
            <div>
                <span class="sbk-contact-kicker">Contact SBK Auto Trading</span>
                <h1>Talk to a real export specialist before you buy.</h1>
                <p>Send your vehicle request, shipment question, or inspection concern to SBK. Our team can guide you through selection, verification, documentation, and export support.</p>
                <div class="sbk-contact-crumb"><b>Home</b><span>/</span><span>Contact Us</span></div>
            </div>

            <div class="sbk-contact-card">
                <strong>Need a faster answer?</strong>
                <p>For urgent vehicle sourcing and shipment questions, message SBK on WhatsApp and include the car name, destination country, and budget range.</p>
                <a href="https://wa.me/817094275106" target="_blank" rel="noopener">WhatsApp SBK</a>
            </div>
        </div>
    </div>

    <div class="container sbk-contact-strip">
        <div class="sbk-contact-strip__inner">
            <div class="sbk-contact-pill"><i>01</i><div><b>Vehicle Sourcing</b><span>Tell us model, year, mileage, and target budget.</span></div></div>
            <div class="sbk-contact-pill"><i>02</i><div><b>Import Guidance</b><span>Ask about inspection, documents, and shipment steps.</span></div></div>
            <div class="sbk-contact-pill"><i>03</i><div><b>Global Offices</b><span>Reach SBK teams across UAE, Japan, USA, Kenya, Jamaica, and Pakistan.</span></div></div>
        </div>
    </div>

    <div class="container sbk-contact-body">
        <div class="sbk-contact-layout">
            <div class="sbk-contact-panel">
                <div class="sbk-contact-form-head">
                    <span>Send Your Request</span>
                    <h2>We will get back to you with clear next steps.</h2>
                </div>

                <form class="sbk-contact-form" action="{{ route('contact-us.submit') }}" method="post">
                    @csrf

                    @if(session('success'))
                        <div class="sbk-contact-alert sbk-contact-alert--success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="sbk-contact-alert sbk-contact-alert--error">
                            Please check the form and try again.
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="sbk-contact-field">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" required>
                    </div>

                    <div class="sbk-contact-field">
                        <label for="email">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                    </div>

                    <div class="sbk-contact-field">
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="+1 000 000 0000">
                    </div>

                    <div class="sbk-contact-field">
                        <label for="subject">Subject</label>
                        <input id="subject" name="subject" type="text" value="{{ old('subject') }}" placeholder="Vehicle inquiry">
                    </div>

                    <div class="sbk-contact-field sbk-contact-field--full">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us what you want to import, your destination country, and any specific questions.">{{ old('message') }}</textarea>
                    </div>

                    <div class="sbk-contact-field sbk-contact-field--full">
                        <button class="sbk-contact-submit" type="submit">Submit Message</button>
                    </div>
                </form>
            </div>

            <aside class="sbk-contact-side">
                <div class="sbk-contact-box">
                    <h3>Direct Contact</h3>
                    <div class="sbk-contact-info">
                        <a href="tel:+817094275106"><i>P</i><div><b>Phone</b><span>+81 70-9427-5106</span></div></a>
                        <a href="mailto:info@sbkautotrading.com"><i>E</i><div><b>Email</b><span>info@sbkautotrading.com</span></div></a>
                        <div><i>H</i><div><b>Working Hours</b><span>Mon - Fri: 10:00 - 18:00</span></div></div>
                    </div>
                </div>

                <div class="sbk-contact-box">
                    <h3>Best To Include</h3>
                    <div class="sbk-contact-info">
                        <div><i>1</i><div><b>Vehicle Details</b><span>Make, model, year, mileage, and preferred condition.</span></div></div>
                        <div><i>2</i><div><b>Destination</b><span>Country and port, so the team can guide shipment steps.</span></div></div>
                        <div><i>3</i><div><b>Budget Range</b><span>Purchase budget plus import and delivery expectations.</span></div></div>
                    </div>
                </div>

                <div class="sbk-contact-box sbk-contact-whatsapp">
                    <h3>Live Vehicle Help</h3>
                    <p>For quick availability checks, send screenshots or stock links directly to SBK on WhatsApp.</p>
                    <a href="https://wa.me/817094275106" target="_blank" rel="noopener">Open WhatsApp</a>
                </div>
            </aside>
        </div>
    </div>

    <div class="sbk-contact-offices">
        <div class="container">
            <div class="sbk-contact-heading">
                <div>
                    <span class="sbk-contact-kicker" style="color:var(--red)">Global Reach</span>
                    <h2>Find the nearest SBK office.</h2>
                </div>
                <p>SBK supports international buyers through multiple offices and showroom locations, making vehicle export more transparent from inquiry to shipment.</p>
            </div>

            <div class="sbk-office-grid">
                @foreach($offices as $office)
                    <article class="sbk-office">
                        <small>{{ $office['time'] }}</small>
                        <h3>{{ $office['name'] }}</h3>
                        <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $office['phone']) }}">{{ $office['phone'] }}</a></p>
                        <p>{{ $office['address'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="sbk-contact-map">
                <div>
                    <h3>Premium vehicles, shipped worldwide.</h3>
                    <p>Whether you are buying from Japan, arranging export documents, or checking a car before it reaches your country, SBK is ready to help.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
