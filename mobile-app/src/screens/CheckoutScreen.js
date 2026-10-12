/* Checkout - the website's checkout: buyer details, destination, how much to
   pay now (the same percentage rules as the website), and the order summary.
   It is the website's "Submit Order for Checking": the order is saved on the
   website for SBK to confirm availability, costs and the payment steps; a new
   customer's order makes their account, as on the website. */
import { useEffect, useMemo, useState } from 'react';
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import * as WebBrowser from 'expo-web-browser';

import { C, R, lift, useUI } from '../theme';
import * as api from '../api';
import { CHECKOUT_TEXT } from '../api/static';
import { Button, Divider, Eyebrow, Img, T } from '../components/base';
import { Empty, Notice, PageHero, Wrap } from '../components/layout';
import { Chip, Field, valid } from '../components/form';
import { useShop } from '../store/shop';
import { useAccount } from '../store/account';
import { useGo } from '../nav';
import { money } from '../format';

const PERCENTS = [10, 30, 50, 100];

/* CheckoutController::paymentBreakdown and the page's paymentBreakdown(),
   line for line: at $3,000 or less the least anyone may pay now is 25%. */
export function breakdown(subtotal, selected, amount) {
  const minimum = subtotal <= 3000 ? 25 : 0.01;
  let effective;
  let paid;
  if (amount != null) {
    paid = Math.min(Math.max(Number(amount) || 0, 0), subtotal);
    effective = subtotal > 0 ? (paid / subtotal) * 100 : 0;
    if (effective < minimum) {
      effective = minimum;
      paid = Math.round(subtotal * (effective / 100) * 100) / 100;
    }
  } else {
    const s = Math.min(Math.max(Number(selected) || 0, 0), 100);
    effective = s < minimum ? minimum : s;
    paid = Math.round(subtotal * (effective / 100) * 100) / 100;
  }
  return { effective, paid, due: Math.max(Math.round((subtotal - paid) * 100) / 100, 0) };
}

export default function CheckoutScreen() {
  const ui = useUI();
  const go = useGo();
  const nav = useNavigation();
  const shop = useShop();
  const account = useAccount();
  const user = account.user;
  const [items, setItems] = useState(null);
  const [form, setForm] = useState({
    name: user ? user.name : '',
    email: user ? user.email : '',
    phone: user ? user.phone : '',
    country: user ? user.country : '',
    city: '',
    port: '',
    password: '',
    confirm: '',
    message: '',
  });
  // The account may arrive a moment after the screen: fill what is still empty.
  useEffect(() => {
    if (!user) return;
    setForm((f) => ({
      ...f,
      name: f.name || user.name || '',
      email: f.email || user.email || '',
      phone: f.phone || user.phone || '',
      country: f.country || user.country || '',
    }));
  }, [user]);

  // The percentage is what the buyer chose; the amount follows it, except
  // while the buyer is typing an amount of their own (amountDraft).
  const [percent, setPercent] = useState(null);
  const [amountDraft, setAmountDraft] = useState(null);
  const [errors, setErrors] = useState({});
  const [busy, setBusy] = useState(false);
  const [paying, setPaying] = useState(false);
  const [done, setDone] = useState(null);

  useEffect(() => {
    if (shop.ready) api.getProductsBySlugs(shop.cart).then(setItems);
  }, [shop.ready, shop.cart]);

  const subtotal = (items || []).reduce((s, p) => s + p.price, 0);

  // The website's starting percentage: 30% at $3,000 or less, otherwise 10%.
  useEffect(() => {
    if (items && items.length && percent === null) setPercent(String(subtotal <= 3000 ? 30 : 10));
  }, [items, subtotal, percent]);

  const pay = useMemo(() => breakdown(subtotal, Number(percent) || 0, null), [subtotal, percent]);
  const raised = Number(percent) > 0 && pay.effective !== Number(percent);
  const amount = amountDraft != null ? amountDraft : pay.paid.toFixed(2);

  const set = (k) => (v) => setForm((f) => ({ ...f, [k]: v }));
  const choosePercent = (p) => {
    setPercent(String(p));
    setAmountDraft(null);
  };
  const typePercent = (t) => {
    setPercent(t.replace(/[^0-9.]/g, ''));
    setAmountDraft(null);
  };
  const typeAmount = (t) => setAmountDraft(t.replace(/[^0-9.]/g, ''));
  const settleAmount = () => {
    if (amountDraft == null) return;
    const b = breakdown(subtotal, null, Number(amountDraft) || 0);
    setPercent(String(Number(b.effective.toFixed(2))));
    setAmountDraft(null);
  };

  const checkForm = () => {
    const e = {};
    if (!valid.filled(form.name)) e.name = 'The name field is required.';
    if (!valid.email(form.email)) e.email = 'Enter a valid email address.';
    if (!valid.filled(form.phone)) e.phone = 'The phone field is required.';
    if (!valid.filled(form.country)) e.country = 'The country field is required.';
    if (!(Number(percent) > 0 && Number(percent) <= 100)) e.percent = 'Choose a percentage between 0.01 and 100.';
    if (!user) {
      if (form.password.length < 8) e.password = 'The password must be at least 8 characters.';
      else if (form.password !== form.confirm) e.confirm = 'The password confirmation does not match.';
    }
    setErrors(e);
    if (Object.keys(e).length) {
      Alert.alert('Please check the form', 'Please check the highlighted checkout fields and try again.');
      return false;
    }
    return true;
  };

  const serverErrors = (err) => {
    const fe = {};
    Object.keys(err.errors || {}).forEach((k) => { fe[k === 'password_confirmation' ? 'confirm' : k] = String(err.errors[k][0] || ''); });
    setErrors(fe);
    Alert.alert('Please check the form', Object.keys(fe).length ? 'Please check the highlighted checkout fields and try again.' : err.message);
  };

  /* PayPal or card - the website's own payment page, in the phone's browser;
     when PayPal has taken the payment, the website writes the order and sends
     the phone back here (sbkapp://pay-result). */
  const payOnline = async () => {
    if (!checkForm()) return;
    setPaying(true);
    try {
      const r = await api.startPay(shop.cart, form, Number(percent), pay.paid);
      if (r.signedIn && r.signedIn.token) account.adopt(r.signedIn.token, r.signedIn.user);
      const result = await WebBrowser.openAuthSessionAsync(r.url, r.returnUrl || 'sbkapp://pay-result');
      const url = result && result.type === 'success' ? String(result.url || '') : '';
      const m = /[?&]status=paid&order=([^&]+)/.exec(url);
      if (m) {
        const paid = { orderNo: decodeURIComponent(m[1]), items: items || [], subtotal, paid: r.payNow, online: true };
        shop.clearCart();
        setDone(paid);
      } else if (/status=expired/.test(url)) {
        Alert.alert('Payment page expired', 'Please press "Pay with PayPal or card" again.');
      }
    } catch (err) {
      serverErrors(err);
    } finally {
      setPaying(false);
    }
  };

  const submit = async () => {
    if (!checkForm()) return;
    setBusy(true);
    try {
      const r = await api.checkout(shop.cart, form, Number(percent), pay.paid);
      if (r.signedIn && r.signedIn.token) account.adopt(r.signedIn.token, r.signedIn.user);
      shop.clearCart();
      setDone(r.order);
    } catch (err) {
      serverErrors(err);
    } finally {
      setBusy(false);
    }
  };

  if (done) {
    return (
      <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
        <PageHero eyebrow={CHECKOUT_TEXT.eyebrow} title={CHECKOUT_TEXT.title} compact />
        <Wrap style={{ paddingTop: 16 }}>
          <View style={styles.card}>
            <Eyebrow>{done.online ? 'Payment Received' : CHECKOUT_TEXT.doneLabel}</Eyebrow>
            <T v="h2">{done.online ? 'Payment completed successfully.' : CHECKOUT_TEXT.doneTitle}</T>
            <T v="body" color={C.muted} style={{ marginTop: 10 }}>{done.online ? 'Paid ' + money(done.paid) + ' by PayPal. SBK will confirm the shipment and the next payment steps.' : CHECKOUT_TEXT.doneText}</T>
            <View style={styles.doneBox}>
              <T v="label" color={C.muted}>Order</T>
              <T v="h3" style={{ marginTop: 2 }}>{done.orderNo}</T>
              <T v="small" color={C.muted} weight="500" style={{ marginTop: 4 }}>{done.items.length} {done.items.length === 1 ? 'vehicle' : 'vehicles'} / {money(done.subtotal)}</T>
            </View>
            <Button title="Continue Shopping" onPress={() => go.shop({})} full style={{ marginTop: 16 }} />
            {account.user ? <Button title="View My Orders" kind="outline" onPress={() => go.go('Profile', { tab: 'orders' })} full style={{ marginTop: 10 }} /> : null}
          </View>
        </Wrap>
      </ScrollView>
    );
  }

  if (items && !items.length && !done) {
    return (
      <View style={{ flex: 1, backgroundColor: C.bg }}>
        <Empty icon="cart-outline" title="Your cart is empty." text="Add a vehicle to your cart to checkout." action="Browse vehicles" onAction={() => go.shop()} />
      </View>
    );
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: C.bg }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: 36 }}>
        <PageHero eyebrow={CHECKOUT_TEXT.eyebrow} title={CHECKOUT_TEXT.title} text={CHECKOUT_TEXT.text} compact />
        <Wrap style={{ paddingTop: 16 }}>
          {/* summary */}
          <View style={styles.card}>
            <T v="h3" style={{ marginBottom: 10 }}>Order Summary</T>
            {(items || []).map((p) => (
              <View key={p.slug} style={styles.item}>
                <Img src={p.image} style={styles.thumb} />
                <View style={{ flex: 1, marginLeft: 12 }}>
                  <T v="title" size={13.5} lines={2}>{p.title}</T>
                  <T v="small" color={C.muted} weight="500" style={{ marginTop: 2 }}>Qty 1 / {money(p.price)}</T>
                </View>
              </View>
            ))}
            <Divider style={{ marginTop: 4 }} />
            <Line label="Subtotal" value={money(subtotal)} strong />
            <Line label="Pay Now" value={money(pay.paid)} red />
            <Line label="Remaining" value={money(pay.due)} muted />
            <T v="small" color={C.muted} weight="500" style={{ marginTop: 6 }}>{CHECKOUT_TEXT.summaryNote}</T>
          </View>

          {/* buyer */}
          <View style={[styles.card, { marginTop: 14 }]}>
            <Eyebrow>{CHECKOUT_TEXT.formLabel}</Eyebrow>
            <T v="h3" style={{ marginBottom: 16 }}>{CHECKOUT_TEXT.formTitle}</T>
            <Field label="Full Name" required value={form.name} onChangeText={set('name')} error={errors.name} autoComplete="name" textContentType="name" />
            <Field label="Email Address" required value={form.email} onChangeText={set('email')} error={errors.email} keyboardType="email-address" autoComplete="email" textContentType="emailAddress" />
            <Field label="Phone / WhatsApp" required value={form.phone} onChangeText={set('phone')} error={errors.phone} keyboardType="phone-pad" autoComplete="tel" textContentType="telephoneNumber" />
            <Field label="Destination Country" required value={form.country} onChangeText={set('country')} error={errors.country} autoComplete="country" />
            <View style={ui.width >= 600 ? { flexDirection: 'row', columnGap: 12 } : null}>
              <Field label="City" value={form.city} onChangeText={set('city')} style={ui.width >= 600 ? { flex: 1 } : null} />
              <Field label="Preferred Port" value={form.port} onChangeText={set('port')} placeholder="Mombasa, Kingston, Karachi" style={ui.width >= 600 ? { flex: 1 } : null} />
            </View>

            <T v="label" color={C.ink} style={{ marginBottom: 8 }}>Payment Percentage <T v="label" color={C.red}>*</T></T>
            <View style={{ flexDirection: 'row', flexWrap: 'wrap' }}>
              {PERCENTS.map((p) => (
                <Chip key={p} label={p + '%'} selected={Number(percent) === p} onPress={() => choosePercent(p)} />
              ))}
            </View>
            <View style={ui.width >= 360 ? { flexDirection: 'row', columnGap: 12 } : null}>
              <Field label="Percentage" value={percent || ''} onChangeText={typePercent} keyboardType="decimal-pad" error={errors.percent} style={ui.width >= 360 ? { flex: 1 } : null} hint="Up to 100%" />
              <Field
                label="Pay Now Amount"
                required
                value={amount}
                onChangeText={typeAmount}
                onBlur={settleAmount}
                keyboardType="decimal-pad"
                style={ui.width >= 360 ? { flex: 1.3 } : null}
                hint={'Up to ' + money(subtotal)}
                left="cash-outline"
                onSubmitEditing={settleAmount}
              />
            </View>
            {amountDraft != null ? (
              <Button title="Use this amount" size="sm" kind="outline" onPress={settleAmount} style={{ alignSelf: 'flex-start', marginTop: -6, marginBottom: 14 }} />
            ) : null}
            {raised ? <Notice tone="info" text={CHECKOUT_TEXT.minimumNote} style={{ marginBottom: 16 }} /> : null}

            {!user ? (
              <>
                <Field label="Account Password" required value={form.password} onChangeText={set('password')} secure error={errors.password} autoComplete="new-password" textContentType="newPassword" hint="At least 8 characters. Your SBK account is made with your order." />
                <Field label="Confirm Password" required value={form.confirm} onChangeText={set('confirm')} secure error={errors.confirm} autoComplete="new-password" textContentType="newPassword" />
              </>
            ) : null}
            <Field
              label="Additional Notes"
              value={form.message}
              onChangeText={set('message')}
              multiline
              placeholder="Tell us about shipment timing, inspection needs, or documents you need."
            />

            <View style={styles.payBox}>
              <T v="title">Pay with PayPal or card</T>
              <T v="sub" color={C.muted} style={{ marginTop: 4 }}>Pay {money(pay.paid)} now on SBK's secure PayPal page; the order is confirmed straight away.</T>
              <Button title={'Pay ' + money(pay.paid) + ' Now'} icon="card-outline" kind="dark" onPress={payOnline} loading={paying} disabled={busy} full style={{ marginTop: 12 }} />
            </View>
            <T v="small" color={C.muted} weight="600" center style={{ marginVertical: 12 }}>or send the order first and pay later</T>
            <Button title="Submit Order for Checking" icon="shield-checkmark-outline" onPress={submit} loading={busy} disabled={paying} full style={{ marginTop: 4 }} />
            <Button title="Continue Shopping" kind="outline" onPress={() => nav.goBack()} full style={{ marginTop: 10 }} />
          </View>
        </Wrap>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

function Line({ label, value, strong, red, muted }) {
  return (
    <View style={styles.line}>
      <T v={strong ? 'title' : 'body'} color={muted ? C.muted : C.ink}>{label}</T>
      <T v="h3" size={strong ? 17 : 15.5} weight="900" color={red ? C.red : muted ? C.muted : C.ink}>{value}</T>
    </View>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  item: { flexDirection: 'row', alignItems: 'center', marginBottom: 12 },
  thumb: { width: 74, height: 56, borderRadius: R.sm },
  line: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 7 },
  payBox: { borderWidth: 1, borderColor: C.line, borderRadius: R.md, padding: 14, backgroundColor: C.bg, marginTop: 4 },
  doneBox: { borderWidth: 1, borderColor: C.line, borderRadius: R.md, padding: 14, backgroundColor: C.bg, marginTop: 16 },
});
