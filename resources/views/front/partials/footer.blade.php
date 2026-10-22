 <footer class="footer">
        <div class="container footer__grid">
            <div class="footer__about"><p><a class="footer__logo-link" href="{{ route('index') }}" aria-label="SBK home"><img src="{{ $assetBase }}/img/logo.png" alt="SBK"></a>SBK Auto Trading: Your trusted partner for premium vehicles worldwide, offering unparalleled selection and transparent service. Discover your dream car with confidence, backed by 15 years of global automotive trading expertise.</p>
            </div>
            <div><h3>Quick link</h3><a href="{{ route('index') }}">Home</a><a href="{{ route('shop') }}">Shop</a><a href="{{ route('about-us') }}">About us</a><a href="{{ route('contact-us') }}">Contact us</a><a href="{{ route('bank') }}">Bank Details</a></div>
            <div><h3>By Types</h3><a href="{{ route('shop', ['types' => ['SUV']]) }}">SUV</a><a href="{{ route('shop', ['types' => ['Coupe']]) }}">Coupe</a><a href="{{ route('shop', ['types' => ['Hatchback']]) }}">Hatchback</a><a href="{{ route('shop', ['types' => ['Sedan']]) }}">Sedan</a><a href="{{ route('shop', ['types' => ['Wagon']]) }}">Wagon</a><a href="{{ route('shop', ['types' => ['Van / MPV']]) }}">Van</a><a href="{{ route('shop', ['types' => ['Convertible / Roadster']]) }}">Roadster</a></div>
            <div><h3>By Brands</h3><a href="{{ route('shop', ['brands' => ['Honda']]) }}">Honda</a><a href="{{ route('shop', ['brands' => ['Toyota']]) }}">Toyota</a><a href="{{ route('shop', ['brands' => ['Mazda']]) }}">Mazda</a><a href="{{ route('shop', ['brands' => ['Mitsubishi']]) }}">Mitsubushi</a><a href="{{ route('shop', ['brands' => ['Subaru']]) }}">Subaru</a><a href="{{ route('shop', ['brands' => ['BMW']]) }}">BMW</a><a href="{{ route('shop', ['brands' => ['Suzuki']]) }}">Suzuki</a><a href="{{ route('shop', ['brands' => ['Daihatsu']]) }}">Daihatsu</a><a href="{{ route('shop', ['brands' => ['Lexus']]) }}">Lexus</a><a href="{{ route('shop', ['brands' => ['Hino']]) }}">Hino</a><a href="{{ route('shop', ['brands' => ['Mercedes-Benz']]) }}">Mercedes-Benz</a><a href="{{ route('shop', ['brands' => ['Volkswagen']]) }}">Volkswagen</a><a href="{{ route('shop', ['brands' => ['Audi']]) }}">Audi</a><a href="{{ route('shop', ['brands' => ['Volvo']]) }}">Volvo</a><a href="{{ route('shop', ['brands' => ['Ford']]) }}">Ford</a><a href="{{ route('shop', ['brands' => ['Jeep']]) }}">Jeep</a><a href="{{ route('shop', ['brands' => ['Land Rover']]) }}">Land Rover</a><a href="{{ route('shop', ['brands' => ['Hyundai']]) }}">Hyundai</a></div>
            <div class="footer__offices">
                <h3>Global Offices</h3>
                <div><strong>Headquarter UAE</strong><p>+971-55-417-7311</p><p>Amber Gem Tower, Mezzanine Floor, Sheikh Khalifa Street, P.O. Box 4848, Ajman, United Arab Emirates</p></div>
                <div><strong>USA Office</strong><p>+1 (321)-357-857</p><p>Office Address: Prior Lake, MN 55372, USA</p></div>
                <div><strong>Japan Office</strong><p>+81-70-9427-5106</p><p>Aichi-ken Nagoyashi Midoriku Shikayama 2-1-1 Royal Shikayama A503, Nagoya-shi, Aichi, Japan</p></div>
                <div ><strong style="font-size: 14px !important;">Showroom Mombasa</strong><p>+254-705719918</p><p>Nyere Avenue Showroom No 5 Jdm Motors Behind Kra Mombasa</p></div>
                <div><strong>Jamaica Office</strong><p>+1-876-8036277</p><p>3 South Avenue Kingston 5, Jamaica</p></div>
                <div><strong>Pakistan Office</strong><p>+92-213-339-3946</p><p>SB-36, Gulshan-e-Iqbal 13-D Karachi, Pakistan</p>
                </div>
            </div>
                <nav class="footer__social" aria-label="Social media">
                    <a class="footer__social-link" href="https://www.facebook.com/sbkautotrading.official" target="_blank" rel="noopener noreferrer" aria-label="Facebook (opens in a new tab)" title="Facebook">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.5 21v-8h2.7l.4-3h-3.1V8.1c0-.9.3-1.5 1.6-1.5h1.7V3.9c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3V10H7.3v3h2.8v8h3.4Z"/></svg>
                    </a>
                    <a class="footer__social-link" href="https://www.instagram.com/sbkautotrading/" target="_blank" rel="noopener noreferrer" aria-label="Instagram (opens in a new tab)" title="Instagram">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" focusable="false"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.4" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a class="footer__social-link" href="https://www.linkedin.com/company/sbkglobalautotrading/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn (opens in a new tab)" title="LinkedIn">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M5.2 3a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM3.5 8.5H7V21H3.5V8.5Zm5.8 0h3.4v1.7h.1c.5-1 1.7-2 3.5-2 3.7 0 4.2 2.3 4.2 5.3V21H17v-6.6c0-1.6 0-3-1.9-3s-2.3 1.5-2.3 2.9V21H9.3V8.5Z"/></svg>
                    </a>
                </nav>
        </div>
        <p class="copyright">Copyright 2026 SBKAutotrading All Rights Reserved</p>
    </footer>
