/* Our Locations, Bank Details, Contact Us and FAQ - the website's pages for
   reaching SBK and paying it, with every number tappable and every account
   field copyable. */
import { useEffect, useState } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View } from 'react-native';
import { KeyboardView } from '../components/KeyboardView';
import { useRoute } from '@react-navigation/native';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';
import * as Clipboard from 'expo-clipboard';

import { C, GRAD, R, lift, useUI } from '../theme';
import { BANK, CONTACT_PAGE, FAQ, LOCATIONS } from '../api/static';
import { Button, Eyebrow, Pill, Press, T } from '../components/base';
import { Notice, PageHero, SectionHead, Wrap } from '../components/layout';
import { Field, valid } from '../components/form';
import { useAccount } from '../store/account';
import { useGo } from '../nav';
import { CONTACT } from '../config';
import { callNumber, emailTo, openMap, whatsApp } from '../actions';
import { clockIn } from '../format';
import * as api from '../api';

/* The time now, refreshed every half minute - for the offices' clocks. */
function useNow() {
  const [now, setNow] = useState(new Date());
  useEffect(() => {
    const t = setInterval(() => setNow(new Date()), 30000);
    return () => clearInterval(t);
  }, []);
  return now;
}

/* ------------------------------------------------------------ locations */

export function LocationsScreen() {
  const now = useNow();
  const l = LOCATIONS;
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero eyebrow={l.introTitle} title={l.title} text={l.intro} compact />
      <Wrap style={{ paddingTop: 18 }}>
        {l.offices.map((o) => (
          <View key={o.title} style={[styles.card, { marginBottom: 14 }]}>
            <View style={{ flexDirection: 'row', alignItems: 'flex-start' }}>
              <T v="h3" style={{ flex: 1, paddingRight: 10 }}>{o.title}</T>
              <Pill text={'Local ' + clockIn(o.zone, now)} tone="wash" />
            </View>
            <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{o.description}</T>
            <InfoLine icon="location" label="Address" text={o.address} />
            <InfoLine icon="call" label="Phone" text={o.phone} onPress={() => callNumber(o.phone)} />
            <InfoLine icon="time" label="Working Hours" text={l.hours} />
            <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 14 }}>
              <Button title="Directions" icon="navigate-outline" kind="outline" size="sm" onPress={() => openMap(o.map)} style={{ flex: 1 }} />
              <Button title="Call" icon="call-outline" size="sm" onPress={() => callNumber(o.phone)} style={{ flex: 1 }} />
            </View>
          </View>
        ))}
      </Wrap>
    </ScrollView>
  );
}

function InfoLine({ icon, label, text, onPress }) {
  const body = (
    <View style={styles.infoLine}>
      <View style={styles.infoIcon}>
        <Ionicons name={icon} size={14} color={C.red} />
      </View>
      <View style={{ flex: 1 }}>
        <T v="label" size={10} color={C.muted}>{label}</T>
        <T v="sub" color={onPress ? C.red : C.ink2} weight={onPress ? '700' : '500'} style={{ marginTop: 2 }}>{text}</T>
      </View>
    </View>
  );
  return onPress ? <Press onPress={onPress} accessibilityLabel={label + ' ' + text}>{body}</Press> : body;
}

/* ----------------------------------------------------------------- bank */

export function BankScreen() {
  const ui = useUI();
  const b = BANK;
  const [region, setRegion] = useState(0);
  const [copied, setCopied] = useState('');

  const copy = async (key, value) => {
    await Clipboard.setStringAsync(value);
    setCopied(key);
    setTimeout(() => setCopied((c) => (c === key ? '' : c)), 1800);
  };

  const bank = b.banks[region];
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero eyebrow={b.eyebrow} title={b.title} text={b.text} compact>
        <View style={styles.vault}>
          <Ionicons name="shield-checkmark" size={22} color={C.red} />
          <View style={{ flex: 1, marginLeft: 12 }}>
            <T v="title" color={C.white}>{b.vaultTitle}</T>
            <T v="small" color="rgba(255,255,255,0.72)" weight="500" style={{ marginTop: 2 }}>{b.vaultText}</T>
          </View>
        </View>
      </PageHero>
      <Wrap style={{ paddingTop: 18 }}>
        <View style={styles.card}>
          {b.steps.map(([n, title, text], i) => (
            <View key={n} style={[styles.step, i < b.steps.length - 1 && styles.stepLine]}>
              <LinearGradient colors={GRAD.red} style={styles.stepNum}>
                <T v="title" color={C.white}>{n}</T>
              </LinearGradient>
              <View style={{ flex: 1, marginLeft: 12 }}>
                <T v="title">{title}</T>
                <T v="small" color={C.muted} weight="500" style={{ marginTop: 2 }}>{text}</T>
              </View>
            </View>
          ))}
        </View>

        <SectionHead eyebrow={b.listLabel} title={b.listTitle} lead={b.listText} style={{ marginTop: 24 }} />
        <View style={styles.segment}>
          {b.banks.map((x, i) => (
            <Press key={x.accent} onPress={() => setRegion(i)} style={[styles.segBtn, region === i && styles.segOn]} accessibilityLabel={x.region}>
              <T v="title" size={13.5} color={region === i ? C.white : C.ink2}>{x.accent}</T>
              <T v="small" size={10.5} color={region === i ? 'rgba(255,255,255,0.8)' : C.muted} weight="600" lines={1}>{x.region}</T>
            </Press>
          ))}
        </View>

        <View style={[styles.card, { padding: 0, overflow: 'hidden', marginTop: 12 }]}>
          <LinearGradient colors={GRAD.dark} style={{ padding: 16 }}>
            <T v="label" color="#FF5A60">{bank.region}</T>
            <T v="h2" color={C.white} style={{ marginTop: 4 }}>{bank.name}</T>
            <T v="sub" color="rgba(255,255,255,0.72)">{bank.subtitle}</T>
          </LinearGradient>
          {bank.details.map(([label, value], i) => {
            const key = bank.accent + i;
            const done = copied === key;
            return (
              <View key={key} style={[styles.bankRow, i < bank.details.length - 1 && styles.stepLine]}>
                <View style={{ flex: 1, paddingRight: 10 }}>
                  <T v="label" size={10} color={C.muted}>{label}</T>
                  <T v="title" size={14} selectable style={{ marginTop: 3 }}>{value}</T>
                </View>
                <Press onPress={() => copy(key, value)} style={[styles.copy, done && { backgroundColor: C.okWash, borderColor: C.okWash }]} accessibilityLabel={'Copy ' + label}>
                  <Ionicons name={done ? 'checkmark' : 'copy-outline'} size={15} color={done ? C.ok : C.ink2} />
                  <T v="small" weight="800" color={done ? C.ok : C.ink2} style={{ marginLeft: 4 }}>{done ? 'Copied' : 'Copy'}</T>
                </Press>
              </View>
            );
          })}
        </View>

        <View style={[styles.card, { marginTop: 14 }]}>
          <T v="h3">{b.safetyTitle}</T>
          <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{b.safetyText}</T>
        </View>
        <View style={[styles.card, { marginTop: 14 }]}>
          <T v="h3">{b.confirmTitle}</T>
          <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{b.confirmText}</T>
          <View style={{ flexDirection: ui.width >= 380 ? 'row' : 'column', gap: 10, marginTop: 14 }}>
            <Button title="Email Payments" icon="mail-outline" kind="outline" size="sm" onPress={() => emailTo(CONTACT.paymentEmail, 'Payment confirmation')} style={{ flex: 1 }} />
            <Button title="Call SBK" icon="call-outline" size="sm" onPress={() => callNumber(CONTACT.dial)} style={{ flex: 1 }} />
          </View>
        </View>
      </Wrap>
    </ScrollView>
  );
}

/* --------------------------------------------------------------- contact */

export function ContactScreen() {
  const ui = useUI();
  const route = useRoute();
  const account = useAccount();
  const user = account.user;
  const now = useNow();
  const c = CONTACT_PAGE;
  const [f, setF] = useState({
    name: user ? user.name : '',
    email: user ? user.email : '',
    phone: user ? user.phone : '',
    subject: route.params?.subject || '',
    message: '',
  });
  const [errors, setErrors] = useState({});
  const [state, setState] = useState(null);
  const [busy, setBusy] = useState(false);
  const set = (k) => (v) => setF((x) => ({ ...x, [k]: v }));

  // The account may arrive a moment after the screen: fill what is still empty.
  useEffect(() => {
    if (!user) return;
    setF((x) => ({ ...x, name: x.name || user.name || '', email: x.email || user.email || '', phone: x.phone || user.phone || '' }));
  }, [user]);

  useEffect(() => {
    if (route.params?.subject) setF((x) => ({ ...x, subject: route.params.subject }));
  }, [route.params?.subject, route.params?.at]);

  const submit = async () => {
    const e = {};
    if (!valid.filled(f.name)) e.name = 'The name field is required.';
    if (!valid.email(f.email)) e.email = 'Enter a valid email address.';
    setErrors(e);
    if (Object.keys(e).length) {
      setState({ tone: 'error', text: 'Please check the form and try again.' });
      return;
    }
    setBusy(true);
    try {
      const r = await api.sendContact(f);
      setState({ tone: 'ok', text: r.message || c.success });
      setF((x) => ({ ...x, subject: '', message: '' }));
    } catch (err) {
      const fe = {};
      Object.keys(err.errors || {}).forEach((k) => { fe[k] = String(err.errors[k][0] || ''); });
      setErrors(fe);
      setState({ tone: 'error', text: Object.keys(fe).length ? 'Please check the form and try again.' : err.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <KeyboardView style={{ flex: 1, backgroundColor: C.bg }} behavior={Platform.OS === 'ios' ? 'padding' : 'height'}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: 36 }}>
        <PageHero eyebrow={c.eyebrow} title={c.title} text={c.text} compact>
          <View style={styles.fast}>
            <T v="title" color={C.white}>{c.fast.title}</T>
            <T v="small" color="rgba(255,255,255,0.75)" weight="500" style={{ marginTop: 4 }}>{c.fast.text}</T>
            <Button title={c.fast.button} kind="whatsapp" icon="logo-whatsapp" size="sm" onPress={() => whatsApp()} style={{ alignSelf: 'flex-start', marginTop: 12 }} />
          </View>
        </PageHero>
        <Wrap style={{ paddingTop: 18 }}>
          <View style={styles.card}>
            {c.pills.map(([n, title, text], i) => (
              <View key={n} style={[styles.step, i < c.pills.length - 1 && styles.stepLine]}>
                <T v="h3" color={C.red} style={{ width: 36 }}>{n}</T>
                <View style={{ flex: 1 }}>
                  <T v="title">{title}</T>
                  <T v="small" color={C.muted} weight="500" style={{ marginTop: 2 }}>{text}</T>
                </View>
              </View>
            ))}
          </View>

          <View style={[styles.card, { marginTop: 14 }]}>
            <Eyebrow>{c.formLabel}</Eyebrow>
            <T v="h3" style={{ marginBottom: 16 }}>{c.formTitle}</T>
            {state ? <Notice tone={state.tone} text={state.text} style={{ marginBottom: 16 }} /> : null}
            <Field label="Full Name" value={f.name} onChangeText={set('name')} error={errors.name} placeholder="Your name" autoComplete="name" />
            <Field label="Email Address" value={f.email} onChangeText={set('email')} error={errors.email} placeholder="you@example.com" keyboardType="email-address" autoComplete="email" />
            <Field label="Phone Number" value={f.phone} onChangeText={set('phone')} placeholder="+1 000 000 0000" keyboardType="phone-pad" autoComplete="tel" />
            <Field label="Subject" value={f.subject} onChangeText={set('subject')} placeholder="Vehicle inquiry" />
            <Field label="Message" value={f.message} onChangeText={set('message')} multiline placeholder={c.messagePlaceholder} />
            <Button title="Submit Message" icon="send" onPress={submit} loading={busy} full />
          </View>

          <View style={[styles.card, { marginTop: 14 }]}>
            <T v="h3" style={{ marginBottom: 6 }}>Direct Contact</T>
            <InfoLine icon="call" label="Phone" text={CONTACT.phone} onPress={() => callNumber(CONTACT.dial)} />
            <InfoLine icon="mail" label="Email" text={CONTACT.email} onPress={() => emailTo(CONTACT.email)} />
            <InfoLine icon="time" label="Working Hours" text={CONTACT.hours} />
          </View>

          <View style={[styles.card, { marginTop: 14 }]}>
            <T v="h3" style={{ marginBottom: 4 }}>Best To Include</T>
            {c.include.map(([n, title, text]) => (
              <View key={n} style={[styles.step, { paddingVertical: 8 }]}>
                <View style={styles.numDot}><T v="small" color={C.white} weight="800">{n}</T></View>
                <View style={{ flex: 1, marginLeft: 12 }}>
                  <T v="title" size={14}>{title}</T>
                  <T v="small" color={C.muted} weight="500" style={{ marginTop: 2 }}>{text}</T>
                </View>
              </View>
            ))}
          </View>

          <LinearGradient colors={['#147A3E', '#0E5A2D']} style={[styles.card, { marginTop: 14 }]}>
            <T v="h3" color={C.white}>{c.liveTitle}</T>
            <T v="sub" color="rgba(255,255,255,0.85)" style={{ marginTop: 6 }}>{c.liveText}</T>
            <Button title="Open WhatsApp" kind="light" icon="logo-whatsapp" onPress={() => whatsApp()} style={{ marginTop: 14 }} full />
          </LinearGradient>

          <SectionHead eyebrow={c.officesLabel} title={c.officesTitle} lead={c.officesText} style={{ marginTop: 26 }} />
          <View style={styles.offices}>
            {c.offices.map((o) => (
              <View key={o.name} style={[styles.card, { width: ui.width >= 600 ? '48.5%' : '100%', marginBottom: 12 }]}>
                <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                  <T v="label" size={10} color={C.muted} style={{ flex: 1 }}>{o.zone}</T>
                  <T v="small" weight="800" color={C.red}>{clockIn(o.zone, now)}</T>
                </View>
                <T v="h3" style={{ marginTop: 4 }}>{o.name}</T>
                <Press onPress={() => callNumber(o.phone)} style={{ minHeight: 36, justifyContent: 'center' }} accessibilityLabel={'Call ' + o.name}>
                  <T v="title" size={14} color={C.red}>{o.phone}</T>
                </Press>
                <T v="sub" color={C.muted}>{o.address}</T>
              </View>
            ))}
          </View>

          <LinearGradient colors={GRAD.dark} style={[styles.card, { marginTop: 4 }]}>
            <T v="h3" color={C.white}>{c.closingTitle}</T>
            <T v="sub" color="rgba(255,255,255,0.75)" style={{ marginTop: 6 }}>{c.closingText}</T>
          </LinearGradient>
        </Wrap>
      </ScrollView>
    </KeyboardView>
  );
}

/* ------------------------------------------------------------------- FAQ */

export function FaqScreen() {
  const go = useGo();
  const [open, setOpen] = useState(0);
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero eyebrow="FAQ" title={FAQ.title} compact />
      <Wrap style={{ paddingTop: 18 }}>
        <View style={[styles.card, { paddingVertical: 4 }]}>
          {FAQ.questions.map((q, i) => (
            <View key={q} style={i < FAQ.questions.length - 1 ? styles.stepLine : null}>
              <Press onPress={() => setOpen(open === i ? -1 : i)} style={styles.faqQ} accessibilityLabel={q}>
                <T v="title" style={{ flex: 1, paddingRight: 10 }}>{q}</T>
                <Ionicons name={open === i ? 'remove-circle' : 'add-circle-outline'} size={22} color={C.red} />
              </Press>
              {open === i ? <T v="sub" color={C.muted} style={{ paddingBottom: 14 }}>{FAQ.answer}</T> : null}
            </View>
          ))}
        </View>
        <LinearGradient colors={GRAD.redDeep} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={[styles.card, { marginTop: 16 }]}>
          <T v="h3" color={C.white}>Still have a question?</T>
          <T v="sub" color="rgba(255,255,255,0.85)" style={{ marginTop: 6 }}>Our team can guide you with details and next steps.</T>
          <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 14 }}>
            <Button title="Live Chat" kind="light" icon="chatbubbles-outline" onPress={() => go.chat()} style={{ flex: 1 }} />
            <Button title="Contact Us" kind="ghostLight" onPress={() => go.contact()} style={{ flex: 1 }} />
          </View>
        </LinearGradient>
      </Wrap>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  infoLine: { flexDirection: 'row', alignItems: 'flex-start', marginTop: 12 },
  infoIcon: { width: 28, height: 28, borderRadius: 14, backgroundColor: C.redWash, alignItems: 'center', justifyContent: 'center', marginRight: 10 },
  vault: { flexDirection: 'row', alignItems: 'flex-start', marginTop: 18, padding: 14, borderRadius: R.lg, backgroundColor: 'rgba(255,255,255,0.06)', borderWidth: 1, borderColor: 'rgba(255,255,255,0.1)' },
  step: { flexDirection: 'row', alignItems: 'center', paddingVertical: 12 },
  stepLine: { borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  stepNum: { width: 34, height: 34, borderRadius: 17, alignItems: 'center', justifyContent: 'center' },
  segment: { flexDirection: 'row', backgroundColor: C.card, borderRadius: R.md, padding: 4, ...lift },
  segBtn: { flex: 1, alignItems: 'center', justifyContent: 'center', minHeight: 50, borderRadius: R.sm, paddingHorizontal: 4 },
  segOn: { backgroundColor: C.red },
  bankRow: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 16, paddingVertical: 12 },
  copy: { flexDirection: 'row', alignItems: 'center', minHeight: 36, paddingHorizontal: 10, borderRadius: R.pill, borderWidth: 1, borderColor: C.line, backgroundColor: C.card },
  fast: { marginTop: 18, padding: 14, borderRadius: R.lg, backgroundColor: 'rgba(255,255,255,0.06)', borderWidth: 1, borderColor: 'rgba(255,255,255,0.1)' },
  numDot: { width: 26, height: 26, borderRadius: 13, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center' },
  offices: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  faqQ: { flexDirection: 'row', alignItems: 'center', minHeight: 56, paddingVertical: 10 },
});
