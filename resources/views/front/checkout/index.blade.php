@extends('front.layouts.app')

@php $assetBase = asset('assets/front'); @endphp

@section('title', 'Checkout | SBK Global Auto Trading')
@section('content')
@php
    $checkoutUser = auth()->user();
    $paypalClientId = config('services.paypal.client_id');
    $defaultPercentage = $subtotal > 0 && $subtotal <= 3000 ? 30 : 10;
@endphp
<style>
.sbk-checkout{--red:#d71920;--dark:#08090b;--ink:#111216;--muted:#6a6f78;--line:#e7e8eb;background:#fff;color:var(--ink);overflow:hidden}.sbk-checkout *{box-sizing:border-box}.sbk-checkout .container{width:min(1180px,calc(100% - 38px));max-width:none!important;margin:auto}.sbk-checkout-hero{position:relative;isolation:isolate;padding:78px 0;background:#090a0d;color:#fff}.sbk-checkout-hero:before{content:"";position:absolute;inset:0;z-index:-2;background:linear-gradient(90deg,rgba(0,0,0,.94),rgba(0,0,0,.66),rgba(0,0,0,.26)),url("{{ $assetBase }}/img/hero-hilux.webp") center/cover no-repeat}.sbk-checkout-hero:after{content:"CHECKOUT";position:absolute;right:-52px;bottom:-45px;color:rgba(255,255,255,.06);font:900 132px/1 Arial,sans-serif;letter-spacing:-7px;z-index:-1}.sbk-checkout-kicker{display:inline-flex;align-items:center;gap:12px;color:#ff4a52;font-size:12px;font-weight:900;letter-spacing:2px;text-transform:uppercase}.sbk-checkout-kicker:before{content:"";width:42px;height:3px;background:var(--red)}.sbk-checkout-hero h1{max-width:760px;margin:16px 0 12px;color:#fff;font-size:clamp(42px,6vw,72px);line-height:.98}.sbk-checkout-hero p{max-width:620px;margin:0;color:rgba(255,255,255,.78);font-size:16px;line-height:1.7}.sbk-checkout-body{padding:58px 0 100px}.sbk-checkout-alert{margin-bottom:20px;padding:14px 16px;border-radius:7px}.sbk-checkout-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}.sbk-checkout-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-checkout-grid{display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:30px;align-items:start}.sbk-checkout-panel,.sbk-summary{border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:0 20px 55px rgba(17,18,20,.09);overflow:hidden}.sbk-panel-head{padding:26px 30px;background:linear-gradient(135deg,#111216,#050505);color:#fff}.sbk-panel-head span{display:block;color:#ff4a52;font-size:11px;font-weight:900;letter-spacing:1.6px;text-transform:uppercase}.sbk-panel-head h2{margin:8px 0 0;color:#fff;font-size:30px}.sbk-checkout-form{display:grid;grid-template-columns:1fr 1fr;gap:18px;padding:30px}.sbk-field{display:grid;gap:8px}.sbk-field--full{grid-column:1/-1}.sbk-field label{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.7px}.sbk-field input,.sbk-field select,.sbk-field textarea{width:100%;border:1px solid #dcdde1;border-radius:5px;background:#fff;color:#111;font:inherit;outline:none}.sbk-field input,.sbk-field select{height:48px;padding:0 13px}.sbk-field textarea{min-height:135px;padding:13px;resize:vertical}.sbk-field input:focus,.sbk-field select:focus,.sbk-field textarea:focus{border-color:var(--red);box-shadow:0 0 0 3px rgba(215,25,32,.09)}.sbk-pay-note{grid-column:1/-1;padding:14px;border-radius:7px;background:#f7f7f8;color:#666;font-size:13px;line-height:1.55}.sbk-paypal-box{grid-column:1/-1}.sbk-card-box{display:block;padding:16px;border:1px solid var(--line);border-radius:8px;background:#fbfbfc}.sbk-card-box h4{margin:0 0 12px;font-size:16px}.sbk-card-row{display:grid;grid-template-columns:minmax(0,1fr) 96px 76px;align-items:center;min-height:50px;border:1px solid #dcdde1;border-radius:5px;background:#fff;overflow:hidden}.sbk-card-row.is-invalid{border-color:#9a1111}.sbk-card-cell{height:50px;padding:13px}.sbk-card-cell+.sbk-card-cell{border-left:1px solid var(--line)}.sbk-card-submit{display:inline-flex;align-items:center;justify-content:center;width:100%;min-height:48px;margin-top:14px;border:0;border-radius:6px;background:var(--red);color:#fff;font-weight:900;cursor:pointer}.sbk-card-submit:disabled{opacity:.65;cursor:not-allowed}.sbk-paypal-error{display:none;margin-top:12px;padding:12px 14px;border-radius:7px;background:#fff1f1;color:#9a1111;border:1px solid #ffcaca;font-size:13px}.sbk-paypal-error.is-open{display:block}.sbk-summary{position:sticky;top:18px}.sbk-summary h3{margin:0;padding:22px 24px;border-bottom:1px solid var(--line);font-size:22px}.sbk-summary-list{display:grid;gap:14px;padding:20px}.sbk-summary-item{display:grid;grid-template-columns:78px 1fr;gap:13px;align-items:center}.sbk-summary-item img{width:78px;height:58px;object-fit:cover;border-radius:6px}.sbk-summary-item h4{margin:0 0 4px;font-size:14px;line-height:1.25}.sbk-summary-item p{margin:0;color:#777;font-size:12px}.sbk-summary-total{display:flex;justify-content:space-between;gap:16px;margin:0 20px 14px;padding-top:18px;border-top:1px solid var(--line);font-size:18px;font-weight:900}.sbk-summary-total--muted{margin-bottom:10px;padding-top:0;border-top:0;font-size:14px;color:#666;font-weight:700}.sbk-summary-total--due{color:var(--red);font-size:20px}.sbk-summary-note{margin:0 20px 22px;padding:14px;border-radius:7px;background:#f7f7f8;color:#666;font-size:13px;line-height:1.55}@media(max-width:920px){.sbk-checkout-grid{grid-template-columns:1fr}.sbk-summary{position:static}}@media(max-width:620px){.sbk-checkout .container{width:calc(100% - 28px)}.sbk-checkout-form{grid-template-columns:1fr;padding:24px}.sbk-card-row{grid-template-columns:minmax(0,1fr) 82px 70px}.sbk-checkout-hero:after{font-size:62px}.sbk-panel-head h2{font-size:25px}}
</style>
<style>
.sbk-paypal-fallback{display:none;margin-top:14px;padding:16px;border:1px solid var(--line);border-radius:8px;background:#fff}.sbk-paypal-fallback.is-open{display:block}.sbk-paypal-fallback h4{margin:0 0 6px;font-size:16px}.sbk-paypal-fallback p{margin:0 0 12px;color:#666;font-size:13px;line-height:1.55}.sbk-checkout-submit{grid-column:1/-1;width:100%;min-height:52px;border:0;border-radius:6px;background:#d71920;color:#fff;font-weight:900;text-transform:uppercase;cursor:pointer;box-shadow:0 14px 26px rgba(215,25,32,.22)}.sbk-checkout-submit:hover{background:#b91218}
</style>

<section class="sbk-checkout">
    <div class="sbk-checkout-hero">
        <div class="container">
            <span class="sbk-checkout-kicker">Secure Vehicle Request</span>
            <h1>Checkout your selected vehicles.</h1>
            <p>Submit your contact and destination details. SBK will confirm availability, total cost, export documents, and shipment options before payment.</p>
        </div>
    </div>

    <div class="container sbk-checkout-body">
        @if(session('success'))
            <div class="sbk-checkout-alert sbk-checkout-alert--success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="sbk-checkout-alert sbk-checkout-alert--error">Please check the highlighted checkout fields and try again.</div>
        @endif

        @if($items->isEmpty())
            <div class="sbk-checkout-panel">
                <div class="sbk-panel-head">
                    <span>Request Received</span>
                    <h2>Thank you. SBK will contact you shortly.</h2>
                </div>
                <div class="sbk-checkout-form" style="display:block">
                    <p style="margin:0 0 22px;color:#555;line-height:1.7">Your checkout request has been saved. Our team will confirm vehicle availability, destination costs, and next payment steps.</p>
                    <a class="sbk-submit" href="{{ route('shop') }}" style="color:#fff;text-decoration:none">Continue Shopping</a>
                </div>
            </div>
        @else
        <div class="sbk-checkout-grid">
            <div class="sbk-checkout-panel">
                <div class="sbk-panel-head">
                    <span>Buyer Details</span>
                    <h2>Where should SBK contact you?</h2>
                </div>
                <form class="sbk-checkout-form" id="sbkCheckoutForm" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="sbk-field">
                        <label for="name">Full Name *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', optional($checkoutUser)->name) }}" required>
                    </div>
                    <div class="sbk-field">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" value="{{ old('email', optional($checkoutUser)->email) }}" required>
                    </div>
                    <div class="sbk-field">
                        <label for="phone">Phone / WhatsApp *</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone', optional($checkoutUser)->phone) }}" required>
                    </div>
                    <div class="sbk-field">
                        <label for="country">Destination Country *</label>
                        <input id="country" name="country" type="text" value="{{ old('country', optional($checkoutUser)->country) }}" required>
                    </div>
                    <div class="sbk-field">
                        <label for="city">City</label>
                        <input id="city" name="city" type="text" value="{{ old('city') }}">
                    </div>
                    <div class="sbk-field">
                        <label for="port">Preferred Port</label>
                        <input id="port" name="port" type="text" value="{{ old('port') }}" placeholder="Mombasa, Kingston, Karachi">
                    </div>
                    <div class="sbk-field sbk-field--full">
                        <label for="payment_percentage">Payment Amount *</label>
                        <select id="payment_percentage" name="payment_percentage" required>
                            @foreach([10 => '10%', 30 => '30%', 50 => '50%', 100 => '100%'] as $value => $label)
                                <option value="{{ $value }}" {!! matchSelected($value, old('payment_percentage', $defaultPercentage)) !!}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @guest
                        <div class="sbk-field">
                            <label for="password">Account Password *</label>
                            <input id="password" name="password" type="password" minlength="8" required>
                        </div>
                        <div class="sbk-field">
                            <label for="password_confirmation">Confirm Password *</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
                        </div>
                    @endguest
                    <div class="sbk-field sbk-field--full">
                        <label for="message">Additional Notes</label>
                        <textarea id="message" name="message" placeholder="Tell us about shipment timing, inspection needs, or documents you need.">{{ old('message') }}</textarea>
                    </div>
                    <div class="sbk-pay-note" id="minimumPaymentNote" style="display:none">For vehicles priced at $3,000 or below, the minimum payment is 25% even if 10% is selected.</div>
                    <input type="hidden" name="paypal_order_id" id="paypal_order_id">
                    <div class="sbk-paypal-box">
                        @if($paypalClientId)
                            <div class="sbk-paypal-fallback is-open" id="paypalFallbackBox">
                                <h4>Pay with PayPal</h4>
                                <p>Complete your secure payment with PayPal.</p>
                                <div id="paypal-button-container"></div>
                            </div>
                            <div class="sbk-card-box" id="paypalCardBox">
                                <h4>Pay with card</h4>
                                <div class="sbk-card-row" id="card-field-row">
                                    <div class="sbk-card-cell" id="card-number-field-container"></div>
                                    <div class="sbk-card-cell" id="card-expiry-field-container"></div>
                                    <div class="sbk-card-cell" id="card-cvv-field-container"></div>
                                </div>
                                <button class="sbk-card-submit" id="card-field-submit-button" type="button">Pay with Card</button>
                            </div>
                        @else
                            <div class="sbk-checkout-alert sbk-checkout-alert--error" style="margin:0">PayPal is not configured yet. Add PayPal credentials before accepting checkout payments.</div>
                        @endif
                        <div class="sbk-paypal-error" id="paypalError"></div>
                    </div>
                    <button class="sbk-checkout-submit" type="submit">Submit Order for Checking</button>
                </form>
            </div>

            <aside class="sbk-summary">
                <h3>Order Summary</h3>
                <div class="sbk-summary-list">
                    @foreach($items as $item)
                        <div class="sbk-summary-item">
                            <img src="{{ $item->product->primary_image_url }}" alt="{{ $item->product->title }}">
                            <div>
                                <h4>{{ $item->product->title }}</h4>
                                <p>Qty {{ $item->quantity }} / ${{ number_format($item->product->price, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="sbk-summary-total">
                    <span>Subtotal</span>
                    <strong>${{ number_format($subtotal, 2) }}</strong>
                </div>
                <div class="sbk-summary-total sbk-summary-total--due">
                    <span>Pay Now</span>
                    <strong id="payNowAmount">$0.00</strong>
                </div>
                <div class="sbk-summary-total sbk-summary-total--muted">
                    <span>Remaining</span>
                    <strong id="remainingAmount">$0.00</strong>
                </div>
                <p class="sbk-summary-note">Final invoice may include freight, inspection, documentation, and destination charges after SBK confirms your route.</p>
            </aside>
        </div>
        @endif
    </div>
</section>
@if(!$items->isEmpty())
@if($paypalClientId)
    <script src="https://www.paypal.com/sdk/js?client-id={{ urlencode($paypalClientId) }}&currency=USD&components=buttons,card-fields"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sbkCheckoutForm');
    const percentage = document.getElementById('payment_percentage');
    const payNow = document.getElementById('payNowAmount');
    const remaining = document.getElementById('remainingAmount');
    const note = document.getElementById('minimumPaymentNote');
    const errorBox = document.getElementById('paypalError');
    const paypalOrderInput = document.getElementById('paypal_order_id');
    const cardBox = document.getElementById('paypalCardBox');
    const cardFieldRow = document.getElementById('card-field-row');
    const cardSubmit = document.getElementById('card-field-submit-button');
    const fallbackBox = document.getElementById('paypalFallbackBox');
    const subtotal = Number(@json((float) $subtotal));
    let paypalFallbackRendered = false;

    function money(value) {
        return '$' + Number(value).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function paymentBreakdown() {
        const selected = Number(percentage.value);
        const effective = subtotal <= 3000 && selected < 25 ? 25 : selected;
        const paid = Math.round((subtotal * (effective / 100)) * 100) / 100;
        const due = Math.max(Math.round((subtotal - paid) * 100) / 100, 0);

        payNow.textContent = money(paid);
        remaining.textContent = money(due);
        note.style.display = effective !== selected ? 'block' : 'none';
    }

    function showError(message) {
        errorBox.textContent = message || 'Payment could not be completed. Please try again.';
        errorBox.classList.add('is-open');
    }

    function formDataPayload() {
        const data = new FormData(form);
        const payload = {};
        data.forEach(function (value, key) {
            payload[key] = value;
        });
        return payload;
    }

    function createPaypalOrder() {
        errorBox.classList.remove('is-open');

        if (!form.reportValidity()) {
            return Promise.reject(new Error('Please complete the required checkout fields first.'));
        }

        return fetch('{{ route('checkout.paypal.create') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_percentage: percentage.value
            })
        })
            .then(response => response.json().then(data => ({ok: response.ok, data})))
            .then(result => {
                if (!result.ok) {
                    throw new Error(result.data.message || 'Unable to start PayPal payment.');
                }

                return result.data.id;
            });
    }

    function capturePaypalOrder(orderID) {
        paypalOrderInput.value = orderID;
        const payload = formDataPayload();
        payload.paypal_order_id = orderID;

        return fetch('{{ route('checkout.paypal.capture') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json().then(data => ({ok: response.ok, data})))
            .then(result => {
                if (!result.ok) {
                    throw new Error(result.data.message || 'Unable to complete PayPal payment.');
                }

                window.location.href = result.data.redirect_url;
            });
    }

    percentage.addEventListener('change', paymentBreakdown);
    paymentBreakdown();

    @if($paypalClientId)
    function renderPaypalFallback() {
        if (!fallbackBox || paypalFallbackRendered || !paypal.Buttons) {
            return;
        }

        paypalFallbackRendered = true;
        fallbackBox.classList.add('is-open');

        const buttonConfig = {
            createOrder: createPaypalOrder,
            onApprove: function (data) {
                return capturePaypalOrder(data.orderID || data.orderId)
                    .catch(error => showError(error.message));
            },
            onError: function (error) {
                showError(error.message || 'PayPal payment could not be completed.');
            }
        };

        if (paypal.FUNDING && paypal.FUNDING.CARD) {
            buttonConfig.fundingSource = paypal.FUNDING.CARD;
        }

        paypal.Buttons(buttonConfig).render('#paypal-button-container');
    }

    renderPaypalFallback();

    if (paypal.CardFields && cardBox && cardFieldRow && cardSubmit) {
        const cardFields = paypal.CardFields({
            createOrder: createPaypalOrder,
            onApprove: function (data) {
                return capturePaypalOrder(data.orderID || data.orderId)
                    .catch(error => showError(error.message));
            },
            onError: function (error) {
                showError(error.message || 'Card payment could not be completed.');
            },
            style: {
                input: {
                    color: '#111216',
                    fontSize: '16px',
                    lineHeight: '22px'
                },
                '.invalid': {
                    color: '#9a1111'
                }
            }
        });

        if (cardFields.isEligible()) {
            cardFields.NumberField({placeholder: 'Card number'}).render('#card-number-field-container');
            cardFields.ExpiryField({placeholder: 'MM/YY'}).render('#card-expiry-field-container');
            cardFields.CVVField({placeholder: 'CVV'}).render('#card-cvv-field-container');

            cardSubmit.addEventListener('click', function () {
                errorBox.classList.remove('is-open');
                cardFieldRow.classList.remove('is-invalid');
                cardSubmit.disabled = true;
                cardSubmit.textContent = 'Processing...';

                cardFields.submit()
                    .catch(function (error) {
                        cardFieldRow.classList.add('is-invalid');
                        showError(error.message || 'Please check your card details and try again.');
                    })
                    .finally(function () {
                        cardSubmit.disabled = false;
                        cardSubmit.textContent = 'Pay with Card';
                    });
            });
        } else {
            cardBox.style.display = 'none';
            renderPaypalFallback();
        }
    } else {
        if (cardBox) {
            cardBox.style.display = 'none';
        }
        renderPaypalFallback();
    }
    @endif
});
</script>
@endif
@endsection
