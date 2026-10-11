/* My Profile - the website's My Account page without the CRM invoices (Sir:
   the CRM stays out of the app): website orders, and the profile with its
   picture and password. */
import { useEffect, useState } from 'react';
import { Alert, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View } from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';
import * as ImagePicker from 'expo-image-picker';

import { C, GRAD, R, lift, useUI } from '../theme';
import { Button, Img, Pill, Press, T } from '../components/base';
import { Empty, Notice, Wrap } from '../components/layout';
import { Field, valid } from '../components/form';
import { useAccount } from '../store/account';
import { useGo } from '../nav';
import { initials } from '../format';

export default function ProfileScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const go = useGo();
  const route = useRoute();
  const account = useAccount();
  const user = account.user;
  const [tab, setTab] = useState(route.params?.tab === 'orders' ? 'orders' : 'edit');

  useEffect(() => {
    if (route.params?.tab) setTab(route.params.tab === 'orders' ? 'orders' : 'edit');
  }, [route.params?.tab]);

  useEffect(() => {
    if (account.ready && !user) nav.replace('Login');
  }, [account.ready, user, nav]);

  if (!user) return <View style={{ flex: 1, backgroundColor: C.bg }} />;

  const logout = () => Alert.alert('Logout', 'Sign out of your SBK account on this phone?', [
    { text: 'Cancel', style: 'cancel' },
    { text: 'Logout', style: 'destructive', onPress: () => { account.signOut(); nav.goBack(); } },
  ]);

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: C.bg }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: 36 }}>
        <LinearGradient colors={GRAD.dark} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}>
          <Wrap style={{ paddingTop: 22, paddingBottom: 40 }}>
            <T v="eyebrow" color="#FF5A60">My account</T>
            <T v="h1" color={C.white}>Welcome, {(user.name || 'SBK Customer').split(' ')[0]}</T>
            <T v="sub" color="rgba(255,255,255,0.75)" style={{ marginTop: 6 }}>Your profile and your website orders - all in one place.</T>
          </Wrap>
        </LinearGradient>
        <Wrap style={{ marginTop: -26 }}>
          <View style={styles.me}>
            {user.photo ? (
              <Img src={{ uri: user.photo }} style={styles.avatar} />
            ) : (
              <LinearGradient colors={GRAD.red} style={styles.avatar}>
                <T v="h2" color={C.white}>{initials(user.name || user.email)}</T>
              </LinearGradient>
            )}
            <View style={{ flex: 1, marginLeft: 14 }}>
              <T v="h3" lines={1}>{user.name || 'SBK Customer'}</T>
              <T v="sub" color={C.muted} lines={1}>{user.email}</T>
              {user.phone || user.country ? <T v="small" color={C.muted} weight="500" lines={1}>{[user.phone, user.country].filter(Boolean).join(' · ')}</T> : null}
              {account.preview ? <Pill text="Preview account" tone="grey" style={{ marginTop: 6 }} /> : null}
            </View>
            <Press onPress={logout} style={styles.logout} accessibilityLabel="Logout">
              <Ionicons name="log-out-outline" size={20} color={C.red} />
            </Press>
          </View>

          <View style={styles.tabs}>
            {[['orders', 'Orders', 'receipt-outline'], ['edit', 'Profile', 'person-outline']].map(([key, label, icon]) => (
              <Press key={key} onPress={() => setTab(key)} style={[styles.tab, tab === key && styles.tabOn]} accessibilityLabel={label}>
                <Ionicons name={icon} size={17} color={tab === key ? C.white : C.ink2} />
                <T v="title" size={14} color={tab === key ? C.white : C.ink2} style={{ marginLeft: 6 }}>{label}</T>
              </Press>
            ))}
          </View>

          {tab === 'orders' ? <Orders onShop={() => go.shop({})} /> : <EditProfile user={user} account={account} />}
        </Wrap>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

function Orders({ onShop }) {
  return (
    <View style={styles.panel}>
      <T v="eyebrow" color={C.red}>Website orders</T>
      <T v="h2">Vehicle orders</T>
      <Empty icon="receipt-outline" title="No order placed on the website yet." text="Orders you place on the website or in this app appear here, with what is paid and what remains." action="Browse vehicles" onAction={onShop} style={{ paddingVertical: 30 }} />
    </View>
  );
}

function EditProfile({ user, account }) {
  const [f, setF] = useState({ name: user.name, email: user.email, phone: user.phone, country: user.country, photo: user.photo });
  const [errors, setErrors] = useState({});
  const [saved, setSaved] = useState('');
  const [pw, setPw] = useState({ current: '', next: '', confirm: '' });
  const [pwState, setPwState] = useState(null);
  const set = (k) => (v) => setF((x) => ({ ...x, [k]: v }));
  const setP = (k) => (v) => setPw((x) => ({ ...x, [k]: v }));

  const pick = async () => {
    const r = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ['images'], allowsEditing: true, aspect: [1, 1], quality: 0.7 });
    if (!r.canceled && r.assets && r.assets[0]) set('photo')(r.assets[0].uri);
  };

  const save = async () => {
    const e = {};
    if (!valid.filled(f.name)) e.name = 'The name field is required.';
    if (!valid.email(f.email)) e.email = 'Enter a valid email address.';
    if (!valid.filled(f.phone)) e.phone = 'The phone field is required.';
    if (!valid.filled(f.country)) e.country = 'The country field is required.';
    setErrors(e);
    setSaved('');
    if (Object.keys(e).length) return;
    await account.update(f);
    setSaved('Profile updated on this phone. It is saved to your website account once the app is connected, in the next step.');
  };

  const changePassword = () => {
    if (!pw.current) return setPwState({ tone: 'error', text: 'The current password field is required.' });
    if (pw.next.length < 8) return setPwState({ tone: 'error', text: 'The new password must be at least 8 characters.' });
    if (pw.next !== pw.confirm) return setPwState({ tone: 'error', text: 'The password confirmation does not match.' });
    return setPwState({ tone: 'preview', text: 'App preview: the password is changed on the website once the app is connected to it, in the next step.' });
  };

  return (
    <>
      <View style={styles.panel}>
        <T v="eyebrow" color={C.red}>Profile details</T>
        <T v="h2" style={{ marginBottom: 16 }}>Edit profile</T>
        {saved ? <Notice tone="ok" text={saved} style={{ marginBottom: 16 }} /> : null}
        <Press onPress={pick} style={styles.photoRow} accessibilityLabel="Change profile picture">
          {f.photo ? (
            <Img src={{ uri: f.photo }} style={styles.photo} />
          ) : (
            <View style={[styles.photo, { backgroundColor: C.line2, alignItems: 'center', justifyContent: 'center' }]}>
              <Ionicons name="camera-outline" size={22} color={C.muted} />
            </View>
          )}
          <View style={{ marginLeft: 12, flex: 1 }}>
            <T v="label" color={C.ink}>Profile Picture</T>
            <T v="small" color={C.red} weight="800" style={{ marginTop: 4 }}>{f.photo ? 'Change picture' : 'Choose a picture'}</T>
          </View>
        </Press>
        <Field label="Name" value={f.name} onChangeText={set('name')} error={errors.name} autoComplete="name" />
        <Field label="Email" value={f.email} onChangeText={set('email')} error={errors.email} keyboardType="email-address" autoComplete="email" />
        <Field label="Phone" value={f.phone} onChangeText={set('phone')} error={errors.phone} keyboardType="phone-pad" autoComplete="tel" />
        <Field label="Country" value={f.country} onChangeText={set('country')} error={errors.country} autoComplete="country" />
        <Button title="Update Profile" onPress={save} full />
      </View>

      <View style={styles.panel}>
        <T v="h3" style={{ marginBottom: 14 }}>Change password</T>
        {pwState ? <Notice tone={pwState.tone} text={pwState.text} style={{ marginBottom: 16 }} /> : null}
        <Field label="Current Password" value={pw.current} onChangeText={setP('current')} secure autoComplete="current-password" textContentType="password" />
        <Field label="New Password" value={pw.next} onChangeText={setP('next')} secure autoComplete="new-password" textContentType="newPassword" />
        <Field label="Confirm Password" value={pw.confirm} onChangeText={setP('confirm')} secure autoComplete="new-password" textContentType="newPassword" />
        <Button title="Change Password" kind="dark" onPress={changePassword} full />
      </View>
    </>
  );
}

const styles = StyleSheet.create({
  me: { flexDirection: 'row', alignItems: 'center', backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  avatar: { width: 64, height: 64, borderRadius: 32, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  logout: { width: 44, height: 44, borderRadius: 22, backgroundColor: C.redWash, alignItems: 'center', justifyContent: 'center' },
  tabs: { flexDirection: 'row', backgroundColor: C.card, borderRadius: R.md, padding: 4, marginTop: 14, ...lift },
  tab: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', minHeight: 44, borderRadius: R.sm },
  tabOn: { backgroundColor: C.red },
  panel: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, marginTop: 14, ...lift },
  photoRow: { flexDirection: 'row', alignItems: 'center', marginBottom: 16 },
  photo: { width: 64, height: 64, borderRadius: 32, overflow: 'hidden' },
});
