<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $assetBase = $assetBase ?? asset('assets/front');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Japanese & Global Vehicle Exports | SBK Global Auto Trading')</title>
    <meta name="description" content="@yield('meta_description', 'SBK Global Auto Trading exports quality Japanese and global vehicles with sourcing, inspection, documentation and shipment support.')">
    @hasSection('meta_keywords')
        <meta name="keywords" content="@yield('meta_keywords')">
    @endif
    <link rel="icon" href="{{ $assetBase }}/img/favicon.webp">
    <link rel="stylesheet" href="{{ $assetBase }}/css/home.css">
    <script src="{{ $assetBase }}/js/home.js" defer></script>

    <style>
    /* Footer only — alignment fixes; rest of page unchanged */
    .footer{background:#000!important;color:#fff!important;padding:50px 0 0!important;}
    .footer .footer__grid{display:grid!important;grid-template-columns:2.2fr .8fr .8fr 1.55fr!important;gap:30px!important;align-items:start!important;}
    .footer__about{min-width:0!important;}
    .footer__about p{margin:0!important;max-width:390px!important;color:#fff!important;line-height:1.65!important;font-size:14px!important;}
    .footer__about img{display:block!important;float:none!important;width:240px!important;max-width:100%!important;height:auto!important;margin:0 0 24px!important;object-fit:contain!important;}
    .footer h3{margin:0 0 26px!important;color:#fff!important;font-size:18px!important;font-weight:700!important;}
    .footer a{display:block!important;color:#fff!important;text-decoration:none!important;margin:0 0 14px!important;font-size:14px!important;line-height:1.25!important;}
    .footer .footer__grid > div:not(.footer__about):not(.footer__offices) a::before{content:'*';color:#e30613;font-weight:900;margin-right:10px;}
    .footer .footer__grid > div:nth-child(4){display:grid!important;grid-template-columns:1fr 1fr!important;column-gap:25px!important;}
    .footer .footer__grid > div:nth-child(4) h3{grid-column:1/-1!important;}
    .footer__offices{grid-column:1/-1!important;border-top:1px solid #777!important;padding-top:34px!important;margin-top:18px!important;display:grid!important;grid-template-columns:repeat(6,minmax(0,1fr))!important;gap:0!important;}
    .footer__offices h3{display:none!important;}
    .footer__offices > div{padding:0 22px!important;min-width:0!important;border-right:1px solid #777!important;box-sizing:border-box!important;}
    .footer__offices > div:first-of-type{padding-left:10px!important;}
    .footer__offices > div:last-of-type{border-right:0!important;padding-right:0!important;}
    .footer__offices strong{display:block!important;color:#fff!important;font-size:16px!important;line-height:1.35!important;margin-bottom:22px!important;min-height:43px!important;}
    .footer__offices p{display:grid!important;grid-template-columns:18px minmax(0,1fr)!important;column-gap:8px!important;align-items:start!important;margin:0 0 16px!important;padding:0!important;color:#fff!important;font-size:13px!important;line-height:1.6!important;white-space:normal!important;overflow-wrap:normal!important;word-break:normal!important;}
    .footer__offices p:first-of-type{font-weight:600!important;white-space:nowrap!important;}
    .footer__offices p:first-of-type::before{content:'☎';color:#e30613;font-weight:700;line-height:1.6;}
    .footer__offices p:last-of-type::before{content:'•';color:#e30613;font-size:17px;line-height:1.15;font-weight:900;}
    .footer .copyright{margin:58px auto 0!important;padding:22px 15px!important;border-top:0!important;text-align:center!important;color:#fff!important;background:#000!important;font-size:14px!important;}
    @media(max-width:1000px){.footer .footer__grid{grid-template-columns:1.5fr 1fr 1fr!important}.footer .footer__grid>div:nth-child(4){grid-column:auto!important}.footer__offices{grid-template-columns:repeat(3,1fr)!important}.footer__offices>div{border-bottom:1px solid #444!important;padding:20px!important}.footer__offices strong{min-height:auto!important}}
    @media(max-width:700px){.footer .footer__grid{grid-template-columns:1fr 1fr!important}.footer__about{grid-column:1/-1!important}.footer__offices{grid-template-columns:1fr!important}.footer__offices>div{border-right:0!important;padding:20px 0!important}.footer .footer__grid>div:nth-child(4){grid-template-columns:1fr 1fr!important}}
    @media(max-width:430px){.footer .footer__grid{grid-template-columns:1fr!important}.footer .footer__grid>div:nth-child(4){grid-template-columns:1fr 1fr!important}}
    @media(max-width:600px){.footer{padding:34px 0 0!important}.footer .footer__grid{grid-template-columns:1fr!important;gap:24px!important}.footer__about img{width:180px!important;margin:0 0 16px!important}.footer__about p{max-width:none!important;font-size:13px!important;line-height:1.55!important}.footer h3{margin:0 0 14px!important;font-size:16px!important}.footer a{margin:0 0 10px!important;font-size:13px!important}.footer .footer__grid>div:nth-child(4){grid-template-columns:1fr 1fr!important;column-gap:18px!important}.footer__offices{padding-top:22px!important;margin-top:0!important;gap:0!important}.footer__offices>div{padding:14px 0!important;border-right:0!important;border-bottom:1px solid #333!important}.footer__offices strong{margin-bottom:10px!important;min-height:0!important;font-size:14px!important}.footer__offices p{margin:0 0 8px!important;font-size:12px!important;line-height:1.45!important}.footer .copyright{margin:24px auto 0!important;padding:16px 12px!important;font-size:12px!important}}
    </style>
</head>
<body>
    <div class="site-preloader" aria-hidden="true">
        <div class="site-preloader__mark">
            <img src="{{ $assetBase }}/img/logo.png" alt="">
            <span></span>
        </div>
        <p>Loading Premium Stock</p>
    </div>
    @include('front.partials.header')
    @yield('content')
    @include('front.partials.footer')

   

    <main>
        </body>
</html>
