{{--
    The SBK app's payment page (Api\App\PayController@page), opened in the
    phone's browser: the website checkout's own PayPal and card buttons, for the
    amount the app's checkout worked out. When PayPal has taken the payment the
    order is written and the browser hands the phone back to the app.
--}}
@php
    $money = fn ($v) => '$' . number_format((float) $v, 2);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="robots" content="noindex">
<title>Secure payment - SBK Global Auto Trading</title>
<link rel="icon" href="{{ asset('assets/front/img/favicon.webp') }}">
<style>
  *{box-sizing:border-box}
  body{margin:0;font-family:Inter,"Segoe UI",Roboto,Arial,sans-serif;background:#f4f5f7;color:#111216}
  .top{background:linear-gradient(135deg,#1a1c22,#0b0c0f);color:#fff;padding:22px 18px 40px}
  .top img{height:34px}
  .top small{display:block;color:#ff5a60;font-size:11px;font-weight:900;letter-spacing:1.4px;text-transform:uppercase;margin-top:18px}
  .top h1{margin:6px 0 0;font-size:24px;line-height:1.15;font-weight:900}
  .wrap{max-width:520px;margin:-24px auto 0;padding:0 16px 40px}
  .card{background:#fff;border-radius:16px;padding:16px;box-shadow:0 6px 14px rgba(17,18,22,.08);margin-bottom:14px}
  .item{display:flex;justify-content:space-between;gap:12px;padding:8px 0;font-size:14px}
  .item b{font-weight:700}
  .line{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-top:1px solid #e4e6ea;font-size:15px}
  .line strong{font-size:17px;font-weight:900}
  .line.red strong{color:#d71920}
  .line.muted{color:#6b717b}
  .note{font-size:12px;color:#6b717b;line-height:1.5;margin:8px 0 0}
  h2{font-size:17px;margin:0 0 12px}
  .cells{display:grid;grid-template-columns:1fr;gap:10px}
  .cell{min-height:48px;border:1px solid #dfe2e7;border-radius:10px;background:#fafbfc}
  .pay-card{width:100%;min-height:50px;border:0;border-radius:12px;background:linear-gradient(135deg,#d71920,#a90f15);color:#fff;font-size:15px;font-weight:800;margin-top:12px}
  .pay-card[disabled]{opacity:.6}
  .err{display:none;background:#fdecec;color:#d71920;border-radius:12px;padding:12px;font-size:14px;margin-bottom:14px}
  .err.on{display:block}
  .ok{background:#e7f5ec;color:#1f9254;border-radius:12px;padding:16px;font-size:15px;text-align:center}
  .back{display:block;text-align:center;color:#d71920;font-weight:800;text-decoration:none;padding:14px;font-size:14px}
  .or{text-align:center;color:#9aa0a8;font-size:12px;margin:14px 0}
</style>
@if ($pay && $clientId)
<script src="https://www.paypal.com/sdk/js?client-id={{ urlencode($clientId) }}&currency=USD&components=buttons,card-fields"></script>
@endif
</head>
<body>
<div class="top">
  <img src="{{ asset('assets/front/img/logo.png') }}" alt="SBK Global Auto Trading">
  <small>Secure Vehicle Payment</small>
  <h1>Pay with PayPal or card</h1>
</div>
<div class="wrap">
  @if (!$pay)
    <div class="card">
      <h2>This payment page has expired</h2>
      <p class="note">Please go back to the SBK app and press "Pay with PayPal or card" again.</p>
    </div>
    <a class="back" href="sbkapp://pay-result?status=expired">Back to the SBK app</a>
  @elseif (!$clientId)
    <div class="card"><div class="err on" style="margin:0">PayPal is not configured yet.</div></div>
    <a class="back" href="sbkapp://pay-result?status=cancel">Back to the SBK app</a>
  @else
    <div class="card">
      <h2>Order Summary</h2>
      @foreach ($products as $p)
        <div class="item"><span>{{ \App\Services\App\AppPresenter::clean($p->title) }}</span><b>{{ $money($p->price) }}</b></div>
      @endforeach
      <div class="line"><span>Subtotal</span><strong>{{ $money($pay['subtotal']) }}</strong></div>
      <div class="line red"><span>Pay Now</span><strong>{{ $money($pay['payment']['paid_amount']) }}</strong></div>
      <div class="line muted"><span>Remaining</span><strong>{{ $money($pay['payment']['remaining_amount']) }}</strong></div>
      <p class="note">Final invoice may include freight, inspection, documentation, and destination charges after SBK confirms your route.</p>
    </div>
    <div class="err" id="err"></div>
    <div class="card" id="payBox">
      <h2>Pay with card</h2>
      <div class="cells" id="cardRow">
        <div class="cell" id="card-number"></div>
        <div class="cell" id="card-expiry"></div>
        <div class="cell" id="card-cvv"></div>
      </div>
      <button class="pay-card" id="cardPay" type="button">Pay {{ $money($pay['payment']['paid_amount']) }} with Card</button>
      <div class="or">or</div>
      <div id="paypal-buttons"></div>
    </div>
    <div class="ok" id="done" style="display:none"></div>
    <a class="back" id="back" href="sbkapp://pay-result?status=cancel">Back to the SBK app</a>
  @endif
</div>
@if ($pay && $clientId)
<script>
(function () {
  var key = @json($key);
  var errBox = document.getElementById('err');
  function showError(m) { errBox.textContent = m || 'Payment could not be completed. Please try again.'; errBox.classList.add('on'); }
  function post(url, body) {
    return fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(body || {}) })
      .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); });
  }
  function createOrder() {
    errBox.classList.remove('on');
    return post(@json(url('/api/app/pay/' . $key . '/create'))).then(function (r) {
      if (!r.ok) throw new Error(r.d.message || 'Unable to start PayPal payment.');
      return r.d.id;
    });
  }
  function capture(orderID) {
    return post(@json(url('/api/app/pay/' . $key . '/capture')), { orderID: orderID }).then(function (r) {
      if (!r.ok) throw new Error(r.d.message || 'Unable to complete PayPal payment.');
      document.getElementById('payBox').style.display = 'none';
      var done = document.getElementById('done');
      done.textContent = r.d.message + ' - taking you back to the SBK app...';
      done.style.display = 'block';
      var back = document.getElementById('back');
      back.href = r.d.redirect;
      back.textContent = 'Back to the SBK app';
      setTimeout(function () { window.location.href = r.d.redirect; }, 1200);
    });
  }
  var buttons = { createOrder: createOrder, onApprove: function (data) { return capture(data.orderID || data.orderId).catch(function (e) { showError(e.message); }); }, onError: function (e) { showError(e && e.message); } };
  if (window.paypal && paypal.Buttons) { paypal.Buttons(buttons).render('#paypal-buttons'); }
  var row = document.getElementById('cardRow'), btn = document.getElementById('cardPay');
  if (window.paypal && paypal.CardFields) {
    var cf = paypal.CardFields({ createOrder: createOrder, onApprove: buttons.onApprove, onError: function (e) { showError(e && e.message || 'Card payment could not be completed.'); },
      style: { input: { color: '#111216', fontSize: '16px', lineHeight: '22px' }, '.invalid': { color: '#9a1111' } } });
    if (cf.isEligible()) {
      cf.NumberField({ placeholder: 'Card number' }).render('#card-number');
      cf.ExpiryField({ placeholder: 'MM/YY' }).render('#card-expiry');
      cf.CVVField({ placeholder: 'CVV' }).render('#card-cvv');
      btn.addEventListener('click', function () {
        errBox.classList.remove('on'); btn.disabled = true; btn.textContent = 'Processing...';
        cf.submit().catch(function (e) { showError(e && e.message || 'Please check your card details and try again.'); })
          .finally(function () { btn.disabled = false; btn.textContent = 'Pay with Card'; });
      });
    } else { row.style.display = 'none'; btn.style.display = 'none'; }
  } else { row.style.display = 'none'; btn.style.display = 'none'; }
})();
</script>
@endif
</body>
</html>
