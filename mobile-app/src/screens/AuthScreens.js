/* Login, Create Account and Forgot Password - the website's three account
   pages, with its fields, its words and its checks, against the website's own
   accounts: one account for the website and the app. */
import { useRef, useState } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';

import { C, R, lift } from '../theme';
import { AUTH_TEXT } from '../api/static';
import { Button, Eyebrow, Press, T } from '../components/base';
import { Notice, PageHero, Wrap } from '../components/layout';
import { Check, Field, valid } from '../components/form';
import { useAccount } from '../store/account';

function AuthPage({ text, children }) {
  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: C.bg }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: 36 }}>
        <PageHero title={text.title} text={text.text} compact />
        <Wrap style={{ marginTop: -14 }}>
          <View style={styles.card}>
            <Eyebrow>{text.label}</Eyebrow>
            <T v="h2" style={{ marginBottom: 16 }}>{text.heading}</T>
            {children}
          </View>
        </Wrap>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

function Link({ label, onPress }) {
  return (
    <Press onPress={onPress} style={{ minHeight: 44, justifyContent: 'center' }} accessibilityLabel={label}>
      <T v="title" size={14} color={C.red} weight="800">{label}</T>
    </Press>
  );
}

/* The website's field errors ({ email: ['...'] }) as one message per field. */
function fieldErrors(e) {
  const out = {};
  if (e && e.errors) {
    Object.keys(e.errors).forEach((k) => {
      const key = k === 'password_confirmation' ? 'confirm' : k;
      out[key] = String((e.errors[k] || [])[0] || '');
    });
  }
  return out;
}

export function LoginScreen() {
  const nav = useNavigation();
  const account = useAccount();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [remember, setRemember] = useState(true);
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const pass = useRef(null);

  const submit = async () => {
    if (!valid.email(email) || !password) {
      setError('Please check your email and password.');
      return;
    }
    setError('');
    setBusy(true);
    try {
      await account.signIn({ email, password });
      nav.goBack();
    } catch (e) {
      setError(e.message || 'Please check your email and password.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <AuthPage text={AUTH_TEXT.login}>
      {error ? <Notice tone="error" text={error} style={{ marginBottom: 16 }} /> : null}
      <Field label="Email Address" value={email} onChangeText={setEmail} keyboardType="email-address" autoComplete="email" textContentType="emailAddress" returnKeyType="next" onSubmitEditing={() => pass.current?.focus()} />
      <Field label="Password" value={password} onChangeText={setPassword} secure inputRef={pass} autoComplete="current-password" textContentType="password" returnKeyType="go" onSubmitEditing={submit} />
      <View style={styles.row}>
        <Check checked={remember} onPress={() => setRemember((r) => !r)} label="Remember me" />
        <Link label="Forgot password?" onPress={() => nav.navigate('Forgot')} />
      </View>
      <Button title="Login" onPress={submit} loading={busy} full style={{ marginTop: 6 }} />
      <View style={[styles.row, { justifyContent: 'center', marginTop: 8 }]}>
        <T v="sub" color={C.muted}>New to SBK? </T>
        <Link label="Create account" onPress={() => nav.replace('Register')} />
      </View>
    </AuthPage>
  );
}

export function RegisterScreen() {
  const nav = useNavigation();
  const account = useAccount();
  const [f, setF] = useState({ name: '', email: '', phone: '', country: '', password: '', confirm: '' });
  const [errors, setErrors] = useState({});
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);
  const set = (k) => (v) => setF((x) => ({ ...x, [k]: v }));

  const submit = async () => {
    const e = {};
    if (!valid.filled(f.name)) e.name = 'The name field is required.';
    if (!valid.email(f.email)) e.email = 'Enter a valid email address.';
    if (!valid.filled(f.phone)) e.phone = 'The phone field is required.';
    if (!valid.filled(f.country)) e.country = 'The country field is required.';
    if (f.password.length < 8) e.password = 'The password must be at least 8 characters.';
    else if (f.password !== f.confirm) e.confirm = 'The password confirmation does not match.';
    setErrors(e);
    setError('');
    if (Object.keys(e).length) return;
    setBusy(true);
    try {
      await account.register(f);
      nav.goBack();
    } catch (err) {
      const fe = fieldErrors(err);
      setErrors(fe);
      setError(Object.keys(fe).length ? 'Please check the highlighted registration fields.' : err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <AuthPage text={AUTH_TEXT.register}>
      {error || Object.keys(errors).length ? <Notice tone="error" text={error || 'Please check the highlighted registration fields.'} style={{ marginBottom: 16 }} /> : null}
      <Field label="Name" value={f.name} onChangeText={set('name')} error={errors.name} autoComplete="name" textContentType="name" />
      <Field label="Email Address" value={f.email} onChangeText={set('email')} error={errors.email} keyboardType="email-address" autoComplete="email" textContentType="emailAddress" />
      <Field label="Phone Number" value={f.phone} onChangeText={set('phone')} error={errors.phone} keyboardType="phone-pad" autoComplete="tel" textContentType="telephoneNumber" />
      <Field label="Country" value={f.country} onChangeText={set('country')} error={errors.country} autoComplete="country" />
      <Field label="Password" value={f.password} onChangeText={set('password')} error={errors.password} secure autoComplete="new-password" textContentType="newPassword" hint="At least 8 characters." />
      <Field label="Confirm Password" value={f.confirm} onChangeText={set('confirm')} error={errors.confirm} secure autoComplete="new-password" textContentType="newPassword" />
      <Button title="Create Account" onPress={submit} loading={busy} full />
      <View style={[styles.row, { justifyContent: 'center', marginTop: 8 }]}>
        <T v="sub" color={C.muted}>Already registered? </T>
        <Link label="Login" onPress={() => nav.replace('Login')} />
      </View>
    </AuthPage>
  );
}

export function ForgotScreen() {
  const nav = useNavigation();
  const account = useAccount();
  const [email, setEmail] = useState('');
  const [state, setState] = useState(null);
  const [busy, setBusy] = useState(false);

  const submit = async () => {
    if (!valid.email(email)) {
      setState({ tone: 'error', text: 'Enter the email address your SBK account uses.' });
      return;
    }
    setBusy(true);
    try {
      setState({ tone: 'ok', text: await account.forgot(email) });
    } catch (e) {
      setState({ tone: 'error', text: e.message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <AuthPage text={AUTH_TEXT.forgot}>
      {state ? <Notice tone={state.tone} text={state.text} style={{ marginBottom: 16 }} /> : null}
      <Field label="Email Address" value={email} onChangeText={setEmail} keyboardType="email-address" autoComplete="email" textContentType="emailAddress" returnKeyType="send" onSubmitEditing={submit} />
      <Button title="Email me a link" onPress={submit} loading={busy} full />
      <View style={{ alignItems: 'center', marginTop: 8 }}>
        <Link label="Back to login" onPress={() => nav.goBack()} />
      </View>
    </AuthPage>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: C.card, borderRadius: R.lg, padding: 18, ...lift },
  row: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' },
});
