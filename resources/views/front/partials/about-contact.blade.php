<style>
.sbk-contact{--red:#d50000;--red-dark:#a80000;--ink:#111214;--muted:#626873;--line:#e7e8eb;--soft:#f5f6f8;--black:#050505;background:#fff;color:var(--ink);overflow:hidden}.sbk-contact *{box-sizing:border-box}.sbk-contact .container{width:min(1180px,calc(100% - 36px));max-width:none!important;margin:0 auto}.sbk-contact a{color:inherit}
.sbk-contact-body{padding:76px 0 92px}.sbk-contact-layout{display:grid;grid-template-columns:minmax(0,1fr) 380px;gap:34px;align-items:start}.sbk-contact-panel{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 20px 55px rgba(17,18,20,.09)}.sbk-contact-form-head{padding:30px 32px;background:linear-gradient(135deg,#101114,#050505);color:#fff}.sbk-contact-form-head span{display:block;color:#ff4d55;font-size:11px;font-weight:900;letter-spacing:1.6px;text-transform:uppercase}.sbk-contact-form-head h2{margin:10px 0 0;color:#fff;font-size:32px;line-height:1.1}.sbk-contact-form{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:30px 32px 34px}.sbk-contact-field{display:grid;gap:8px}.sbk-contact-field--full{grid-column:1/-1}.sbk-contact-field label{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.7px}.sbk-contact-field input,.sbk-contact-field textarea{width:100%;border:1px solid #dcdde1;border-radius:4px;background:#fff;color:#111;font:inherit;outline:none;transition:border-color .2s ease,box-shadow .2s ease}.sbk-contact-field input{height:48px;padding:0 13px}.sbk-contact-field textarea{min-height:150px;resize:vertical;padding:13px}.sbk-contact-field input:focus,.sbk-contact-field textarea:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(213,0,0,.09)}.sbk-contact-submit{display:inline-flex;align-items:center;justify-content:center;width:max-content;min-height:48px;padding:0 24px;border:0;border-radius:4px;background:var(--red);color:#fff;font-size:12px;font-weight:900;text-transform:uppercase;cursor:pointer;box-shadow:0 14px 26px rgba(213,0,0,.18)}.sbk-contact-submit:hover{background:var(--red-dark)}.sbk-contact-alert{grid-column:1/-1;padding:13px 14px;border-radius:4px;font-size:13px;line-height:1.45}.sbk-contact-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}.sbk-contact-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-contact-alert ul{margin:8px 0 0;padding-left:18px}
.sbk-contact-side{display:grid;gap:18px}.sbk-contact-box{overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 14px 35px rgba(17,18,20,.06)}.sbk-contact-box h3{margin:0;padding:19px 21px;border-bottom:1px solid var(--line);background:#fafafa;font-size:18px}.sbk-contact-info{display:grid;gap:14px;padding:20px}.sbk-contact-info>a,.sbk-contact-info>div{display:grid;grid-template-columns:34px minmax(0,1fr);gap:12px;align-items:start}.sbk-contact-info i{display:grid;place-items:center;width:34px;height:34px;border-radius:6px;background:#111;color:#fff;font-style:normal;font-size:13px;font-weight:900}.sbk-contact-info b{display:block;font-size:13px}.sbk-contact-info span{display:block;margin-top:3px;color:var(--muted);font-size:13px;line-height:1.5}.sbk-contact-whatsapp{padding:24px;background:linear-gradient(135deg,#d50000,#101114);color:#fff}.sbk-contact-whatsapp h3{padding:0;border:0;background:transparent;color:#fff;font-size:24px}.sbk-contact-whatsapp p{margin:10px 0 18px;color:rgba(255,255,255,.82);line-height:1.65}.sbk-contact-whatsapp a{display:inline-flex;min-height:42px;align-items:center;padding:0 16px;border-radius:4px;background:#fff;color:#111;font-size:12px;font-weight:900;text-transform:uppercase}
@media(max-width:1050px){.sbk-contact-hero__grid{grid-template-columns:1fr;min-height:0}.sbk-contact-card{max-width:520px}.sbk-contact-layout{grid-template-columns:1fr}.sbk-contact-side{grid-template-columns:1fr 1fr}.sbk-contact-whatsapp{grid-column:1/-1}.sbk-office-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.sbk-contact-hero__grid{padding:58px 0 82px}.sbk-contact-strip__inner{grid-template-columns:1fr}.sbk-contact-pill{border-right:0;border-bottom:1px solid var(--line)}.sbk-contact-pill:last-child{border-bottom:0}.sbk-contact-heading{display:block}.sbk-contact-heading p{margin-top:12px}.sbk-contact-form{grid-template-columns:1fr;padding:24px}.sbk-contact-form-head{padding:26px 24px}.sbk-contact-side{grid-template-columns:1fr}.sbk-office-grid{grid-template-columns:1fr}}
@media(max-width:480px){.sbk-contact .container{width:calc(100% - 28px)}.sbk-contact-hero h1{font-size:39px}.sbk-contact-hero:after{font-size:76px;right:-24px;bottom:-24px}.sbk-contact-card{padding:22px}.sbk-contact-body{padding-top:58px}.sbk-contact-form-head h2,.sbk-contact-heading h2{font-size:28px}}
</style>

<section class="sbk-contact">
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
</section>
