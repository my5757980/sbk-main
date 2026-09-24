@extends('front.layouts.app')

@php
    $assetBase = asset('assets/front');
    $banks = [
        [
            'region' => 'Japan',
            'name' => 'MITSUBISHI UFJ',
            'subtitle' => 'JDM Trading Co. Ltd',
            'accent' => 'JP',
            'details' => [
                'Bank Name' => 'MITSUBISHI UFJ',
                'Bank SWIFT Code' => 'BOTKJPJT',
                'Bank Code' => '0005',
                'Branch Name' => 'TAKBATA BRANCH',
                'Branch Code' => '297',
                'Branch Address' => '1-203, TAKBATA, NAKAGAWA-KU, NAGOYA-SHI, AICHI 454-0911, JAPAN',
                'Account Number' => '0282462',
                'Account Holder\'s Name (Beneficiary\'s Name)' => 'JDM TRADING CO. LTD',
                'Account Holders\' Address (Beneficiary\'s Address)' => 'NAGOYASHI MIDORI-KU-SHIKAYAMA, 2-1-1- ROYAL SHIKAYAMA A503, JAPAN',
                'Account Holders\' Contacts (Beneficiary\'s Contacts)' => 'payment@sbkautotrading.com +81 70 9427 5106',
            ],
        ],
        [
            'region' => 'United Arab Emirates',
            'name' => 'Emirates Islamic',
            'subtitle' => 'SBK Global Auto Trading FZC LLC',
            'accent' => 'AE',
            'details' => [
                'ACCOUNT' => 'SBK GLOBAL AUTO TRADING FZC LLC',
                'BANK' => 'Emirates Islamic',
                'BRANCH' => 'EI Ibn Batuta Mall',
                'ADDRESS' => 'Dubai, United Arab Emirates',
                'IBAN' => 'AE020340003708512932302',
                'ACCOUNT NO' => '3708512932302',
                'SWIFT CODE' => 'MEBLAEADXXX',
            ],
        ],
        [
            'region' => 'United States',
            'name' => 'US Bank',
            'subtitle' => 'SBK Global LLC',
            'accent' => 'US',
            'details' => [
                'ACCOUNT NAME' => 'SBK Global LLC',
                'BANK' => 'US Bank',
                'ROUTING' => '091000022',
                'ACCOUNT NUMBER' => '167505708112',
                'SWIFT' => 'USBKUS44IMT',
                'ACCOUNT NO' => '3708512932302',
                'BANK ADDRESS' => '200 S 6th St Minneapolis, MN 55402',
            ],
        ],
    ];
@endphp

@section('title', 'Bank Details | SBK Global Auto Trading')
@section('meta_description', 'Official SBK Global Auto Trading bank details for Japan, UAE, and USA payments.')

@section('content')
<style>
.sbk-bank{--red:#d71920;--ink:#111216;--muted:#68707b;--line:#e6e8ec;--soft:#f6f7f9;background:#fff;color:var(--ink);overflow:hidden}.sbk-bank *{box-sizing:border-box}.sbk-bank .container{width:min(1160px,calc(100% - 36px));max-width:none!important;margin:0 auto}.sbk-bank-hero{position:relative;isolation:isolate;background:#07080a;color:#fff}.sbk-bank-hero:before{content:"";position:absolute;inset:0;z-index:-3;background:linear-gradient(90deg,rgba(0,0,0,.95),rgba(0,0,0,.76) 44%,rgba(0,0,0,.34)),url("{{ $assetBase }}/img/about-building.webp") center/cover no-repeat}.sbk-bank-hero:after{content:"";position:absolute;inset:auto -8% -42% 42%;z-index:-2;height:360px;background:radial-gradient(circle,rgba(215,25,32,.32),rgba(215,25,32,0) 68%)}.sbk-bank-hero__inner{min-height:450px;display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:42px;align-items:center;padding:70px 0 98px}.sbk-bank-kicker{display:inline-flex;align-items:center;gap:12px;margin-bottom:16px;color:#ff4c54;font-size:12px;font-weight:900;letter-spacing:1.8px;text-transform:uppercase}.sbk-bank-kicker:before{content:"";width:42px;height:3px;background:var(--red)}.sbk-bank-hero h1{max-width:800px;margin:0;color:#fff;font-size:clamp(40px,6vw,72px);line-height:.98;font-weight:900}.sbk-bank-hero p{max-width:640px;margin:20px 0 0;color:rgba(255,255,255,.8);font-size:16px;line-height:1.75}.sbk-bank-vault{position:relative;border:1px solid rgba(255,255,255,.18);border-radius:12px;background:linear-gradient(155deg,rgba(255,255,255,.14),rgba(255,255,255,.06));padding:24px;box-shadow:0 24px 70px rgba(0,0,0,.38);overflow:hidden}.sbk-bank-vault:before{content:"BANK";position:absolute;right:-16px;bottom:-18px;color:rgba(255,255,255,.08);font:900 82px/1 Arial,sans-serif}.sbk-vault-ring{width:96px;height:96px;border:10px solid rgba(255,255,255,.18);border-top-color:#ff4c54;border-radius:50%;margin-bottom:24px}.sbk-bank-vault strong{display:block;position:relative;color:#fff;font-size:28px;line-height:1.1}.sbk-bank-vault span{display:block;position:relative;margin-top:12px;color:rgba(255,255,255,.74);font-size:13px;line-height:1.65}.sbk-bank-ribbon{position:relative;z-index:2;margin-top:-44px}.sbk-bank-ribbon__inner{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 18px 48px rgba(17,18,22,.14);overflow:hidden}.sbk-bank-step{display:grid;grid-template-columns:50px 1fr;gap:14px;align-items:center;min-height:112px;padding:22px;border-right:1px solid var(--line)}.sbk-bank-step:last-child{border-right:0}.sbk-bank-step i{display:grid;place-items:center;width:50px;height:50px;border-radius:50%;background:#111;color:#fff;font-style:normal;font-weight:900;box-shadow:inset 0 0 0 4px #fff,0 0 0 1px #111}.sbk-bank-step b{display:block;font-size:15px}.sbk-bank-step span{display:block;margin-top:5px;color:var(--muted);font-size:12px;line-height:1.45}.sbk-bank-main{padding:76px 0 96px}.sbk-bank-head{display:flex;justify-content:space-between;gap:28px;align-items:flex-end;margin-bottom:30px}.sbk-bank-head h2{margin:0;font-size:34px;line-height:1.1}.sbk-bank-head p{max-width:520px;margin:0;color:var(--muted);line-height:1.65}.sbk-bank-grid{display:grid;grid-template-columns:1fr;gap:24px;align-items:start}.sbk-bank-card{position:relative;display:grid;grid-template-columns:310px minmax(0,1fr);min-height:100%;border:1px solid var(--line);border-radius:12px;background:#fff;box-shadow:0 16px 42px rgba(17,18,22,.08);overflow:hidden}.sbk-bank-card:before{content:attr(data-accent);position:absolute;right:18px;top:12px;color:rgba(17,18,22,.06);font:900 72px/1 Arial,sans-serif}.sbk-bank-card__top{position:relative;padding:24px;background:linear-gradient(150deg,#15171b,#050505);color:#fff}.sbk-bank-card__top small{display:inline-flex;min-height:28px;align-items:center;padding:0 10px;border-radius:999px;background:rgba(215,25,32,.18);color:#ff686f;font-size:10px;font-weight:900;letter-spacing:1.1px;text-transform:uppercase}.sbk-bank-card__top h3{margin:20px 0 0;color:#fff;font-size:25px;line-height:1.08}.sbk-bank-card__top p{margin:10px 0 0;color:rgba(255,255,255,.75);font-size:13px;line-height:1.55}.sbk-bank-table{position:relative;display:grid;padding:10px 0 4px}.sbk-bank-row{display:grid;gap:7px;padding:15px 18px;border-bottom:1px solid #eff0f2}.sbk-bank-row:last-child{border-bottom:0}.sbk-bank-row dt{margin:0;color:#747b86;font-size:11px;font-weight:900;letter-spacing:.55px;text-transform:uppercase}.sbk-bank-value{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:start}.sbk-bank-row dd{margin:0;color:#15171b;font-size:14px;font-weight:800;line-height:1.45;overflow-wrap:anywhere}.sbk-copy{width:32px;height:32px;border:1px solid #e3e5ea;border-radius:6px;background:#fff;color:#111;font-size:11px;font-weight:900;cursor:pointer}.sbk-copy:hover{border-color:#ffd0d0;background:#fff4f4;color:var(--red)}.sbk-bank-note{margin-top:26px;display:grid;grid-template-columns:1.15fr .85fr;gap:18px}.sbk-bank-note article{position:relative;border-radius:10px;padding:24px;border:1px solid var(--line);background:var(--soft);overflow:hidden}.sbk-bank-note article:first-child:after{content:"SAFE";position:absolute;right:-8px;bottom:-20px;color:rgba(17,18,22,.06);font:900 72px/1 Arial,sans-serif}.sbk-bank-note h3{margin:0 0 10px;font-size:20px}.sbk-bank-note p{margin:0;color:var(--muted);line-height:1.65}.sbk-bank-note article:last-child{background:#111;color:#fff;border-color:#111}.sbk-bank-note article:last-child p{color:rgba(255,255,255,.76)}.sbk-toast-copy{position:fixed;left:50%;bottom:24px;z-index:10000;transform:translateX(-50%) translateY(20px);opacity:0;padding:11px 16px;border-radius:999px;background:#111;color:#fff;font-size:12px;font-weight:900;box-shadow:0 18px 45px rgba(0,0,0,.24);transition:opacity .2s ease,transform .2s ease}.sbk-toast-copy.is-open{opacity:1;transform:translateX(-50%) translateY(0)}@media(max-width:980px){.sbk-bank-hero__inner{grid-template-columns:1fr}.sbk-bank-vault{max-width:460px}.sbk-bank-card:before{top:auto;bottom:8px}}@media(max-width:760px){.sbk-bank .container{width:calc(100% - 28px)}.sbk-bank-hero__inner{min-height:0;padding:54px 0 82px}.sbk-bank-hero h1{font-size:39px}.sbk-bank-ribbon__inner{grid-template-columns:1fr}.sbk-bank-step{border-right:0;border-bottom:1px solid var(--line)}.sbk-bank-step:last-child{border-bottom:0}.sbk-bank-head{display:block}.sbk-bank-head p{margin-top:12px}.sbk-bank-card{display:flex;flex-direction:column}.sbk-bank-note{grid-template-columns:1fr}}@media(max-width:460px){.sbk-bank-step{grid-template-columns:44px 1fr;padding:18px}.sbk-bank-step i{width:44px;height:44px}.sbk-bank-card__top{padding:22px}.sbk-bank-row{padding:14px 16px}.sbk-bank-value{grid-template-columns:1fr}.sbk-copy{width:max-content;padding:0 10px}}
</style>

<section class="sbk-bank">
    <div class="sbk-bank-hero">
        <div class="container sbk-bank-hero__inner">
            <div>
                <span class="sbk-bank-kicker">Official Payment Accounts</span>
                <h1>Transfer with clarity, not guesswork.</h1>
                <p>Use the official SBK banking information below only after matching it with your invoice. Every field is arranged for quick checking before you send funds.</p>
            </div>
            <aside class="sbk-bank-vault">
                <div class="sbk-vault-ring" aria-hidden="true"></div>
                <strong>Verify before transfer.</strong>
                <span>Confirm your invoice, destination office, and account selection with the SBK team before making any bank transfer.</span>
            </aside>
        </div>
    </div>

    <div class="container sbk-bank-ribbon">
        <div class="sbk-bank-ribbon__inner">
            <div class="sbk-bank-step"><i>1</i><div><b>Match Invoice</b><span>Check the amount, currency, and beneficiary name.</span></div></div>
            <div class="sbk-bank-step"><i>2</i><div><b>Use Listed Account</b><span>Select Japan, UAE, or USA exactly as instructed.</span></div></div>
            <div class="sbk-bank-step"><i>3</i><div><b>Send Proof</b><span>Share the transfer receipt with SBK after payment.</span></div></div>
        </div>
    </div>

    <div class="container sbk-bank-main">
        <div class="sbk-bank-head">
            <div>
                <span class="sbk-bank-kicker">Banking Details</span>
                <h2>Official account directory.</h2>
            </div>
            <p>Choose the account shown on your invoice. Use the copy buttons carefully and always recheck beneficiary details before submitting a transfer.</p>
        </div>

        <div class="sbk-bank-grid">
            @foreach($banks as $bank)
                <article class="sbk-bank-card" data-accent="{{ $bank['accent'] }}">
                    <div class="sbk-bank-card__top">
                        <small>{{ $bank['region'] }}</small>
                        <h3>{{ $bank['name'] }}</h3>
                        <p>{{ $bank['subtitle'] }}</p>
                    </div>
                    <dl class="sbk-bank-table">
                        @foreach($bank['details'] as $label => $value)
                            <div class="sbk-bank-row">
                                <dt>{{ $label }}</dt>
                                <div class="sbk-bank-value">
                                    <dd>{{ $value }}</dd>
                                    <button class="sbk-copy" type="button" data-copy="{{ $value }}" aria-label="Copy {{ $label }}">Copy</button>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </article>
            @endforeach
        </div>

        <div class="sbk-bank-note">
            <article>
                <h3>Payment safety</h3>
                <p>SBK recommends confirming bank details directly with your sales representative before transfer, especially for international payments.</p>
            </article>
            <article>
                <h3>Need confirmation?</h3>
                <p>Email payment@sbkautotrading.com or contact +81 70 9427 5106 before sending funds.</p>
            </article>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let toast = null;

    function showCopied() {
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'sbk-toast-copy';
            toast.textContent = 'Copied to clipboard';
            document.body.appendChild(toast);
        }

        toast.classList.add('is-open');
        window.clearTimeout(toast.timer);
        toast.timer = window.setTimeout(function () {
            toast.classList.remove('is-open');
        }, 1600);
    }

    document.querySelectorAll('.sbk-copy').forEach(function (button) {
        button.addEventListener('click', function () {
            const value = button.dataset.copy || '';

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(value).then(showCopied);
                return;
            }

            const input = document.createElement('textarea');
            input.value = value;
            input.style.position = 'fixed';
            input.style.opacity = '0';
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            input.remove();
            showCopied();
        });
    });
});
</script>
@endsection
