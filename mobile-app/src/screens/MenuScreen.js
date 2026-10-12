/* Menu - the account and every page of the website's menu and footer. */
import { Alert, ScrollView, StyleSheet, View } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';
import Constants from 'expo-constants';

import { C, GRAD, R, lift, useUI } from '../theme';
import { FOOTER_TEXT, LOGO } from '../api/static';
import { Badge, Button, Img, Press, T } from '../components/base';
import { TabHeader, Wrap } from '../components/layout';
import { useAccount } from '../store/account';
import { useShop } from '../store/shop';
import { useGo } from '../nav';
import { CONTACT } from '../config';
import { callNumber, emailTo, whatsApp } from '../actions';
import { initials } from '../format';

export default function MenuScreen() {
  const ui = useUI();
  const go = useGo();
  const account = useAccount();
  const shop = useShop();
  const user = account.user;

  const logout = () => Alert.alert('Logout', 'Sign out of your SBK account on this phone?', [
    { text: 'Cancel', style: 'cancel' },
    { text: 'Logout', style: 'destructive', onPress: () => account.signOut() },
  ]);

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader title="Menu" />
      <ScrollView contentContainerStyle={{ paddingTop: 16, paddingBottom: 30 }}>
        <Wrap>
          {user ? (
            <Press onPress={() => go.go('Profile')} style={styles.me} accessibilityLabel="My Profile">
              {user.photo ? (
                <Img src={{ uri: user.photo }} style={styles.avatar} />
              ) : (
                <LinearGradient colors={GRAD.red} style={styles.avatar}>
                  <T v="h3" color={C.white}>{initials(user.name || user.email)}</T>
                </LinearGradient>
              )}
              <View style={{ flex: 1, marginLeft: 14 }}>
                <T v="h3" lines={1}>{user.name || 'SBK Customer'}</T>
                <T v="sub" color={C.muted} lines={1}>{user.email}</T>
              </View>
              <Ionicons name="chevron-forward" size={20} color={C.faint} />
            </Press>
          ) : (
            <LinearGradient colors={GRAD.dark} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.welcome}>
              <T v="h2" color={C.white}>Welcome to SBK</T>
              <T v="sub" color="rgba(255,255,255,0.75)" style={{ marginTop: 6 }}>Access your SBK profile, saved details, and complete vehicle order records.</T>
              <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 16 }}>
                <Button title="Login" onPress={() => go.go('Login')} style={{ flex: 1 }} />
                <Button title="Create account" kind="ghostLight" onPress={() => go.go('Register')} style={{ flex: 1 }} />
              </View>
            </LinearGradient>
          )}

          <Group title="Shopping">
            <Row icon="car-sport-outline" label="Shop All Vehicles" onPress={() => go.shop({})} />
            <Row icon="heart-outline" label="Wishlist" count={shop.wish.length} onPress={() => go.go('Wishlist')} />
            <Row icon="cart-outline" label="Cart" count={shop.cart.length} onPress={() => go.tab('Cart')} />
            {user ? <Row icon="receipt-outline" label="My Orders" onPress={() => go.go('Profile', { tab: 'orders' })} /> : null}
          </Group>

          <Group title="SBK Global Auto Trading">
            <Row icon="business-outline" label="About SBK" onPress={() => go.go('About')} />
            <Row icon="ribbon-outline" label="Official Partners" onPress={() => go.go('Partners')} />
            <Row icon="construct-outline" label="Services" onPress={() => go.go('Services')} />
            <Row icon="location-outline" label="Our Locations" onPress={() => go.go('Locations')} />
            <Row icon="card-outline" label="Bank Details" onPress={() => go.go('Bank')} />
            <Row icon="newspaper-outline" label="Blogs" onPress={() => go.go('Blog')} />
            <Row icon="help-circle-outline" label="FAQ" onPress={() => go.go('Faq')} last />
          </Group>

          <Group title="Contact">
            <Row icon="mail-open-outline" label="Contact Us" onPress={() => go.go('Contact')} />
            <Row icon="chatbubbles-outline" label="Live Chat" onPress={() => go.chat()} />
            <Row icon="logo-whatsapp" label="WhatsApp" detail={CONTACT.phone} onPress={() => whatsApp()} />
            <Row icon="call-outline" label="Call" detail={CONTACT.phone} onPress={() => callNumber(CONTACT.dial)} />
            <Row icon="mail-outline" label="Email" detail={CONTACT.email} onPress={() => emailTo(CONTACT.email)} last />
          </Group>

          {user ? (
            <Group title="Account">
              <Row icon="person-circle-outline" label="My Profile" onPress={() => go.go('Profile', { tab: 'edit' })} />
              <Row icon="log-out-outline" label="Logout" danger onPress={logout} last />
            </Group>
          ) : null}

          <View style={styles.footer}>
            <Img src={LOGO} plain fit="contain" style={{ width: ui.s(140), height: ui.s(48) }} />
            <T v="small" color="rgba(255,255,255,0.68)" weight="500" style={{ marginTop: 10 }}>{FOOTER_TEXT}</T>
            <T v="small" color="rgba(255,255,255,0.45)" weight="500" style={{ marginTop: 14 }}>
              Version {Constants.expoConfig?.version || '1.0.0'} · Copyright 2026 SBKAutotrading All Rights Reserved
            </T>
          </View>
        </Wrap>
      </ScrollView>
    </View>
  );
}

function Group({ title, children }) {
  return (
    <View style={{ marginTop: 22 }}>
      <T v="label" color={C.muted} style={{ marginBottom: 8, marginLeft: 4 }}>{title}</T>
      <View style={styles.group}>{children}</View>
    </View>
  );
}

function Row({ icon, label, detail, count, onPress, last, danger }) {
  return (
    <Press onPress={onPress} style={[styles.row, !last && styles.rowLine]} accessibilityLabel={label}>
      <View style={[styles.rowIcon, danger && { backgroundColor: C.redWash }]}>
        <Ionicons name={icon} size={19} color={danger ? C.red : C.ink} />
      </View>
      <View style={{ flex: 1, marginLeft: 12 }}>
        <T v="title" color={danger ? C.red : C.ink}>{label}</T>
        {detail ? <T v="small" color={C.muted} weight="500" lines={1}>{detail}</T> : null}
      </View>
      {count ? <Badge count={count} style={{ position: 'relative', marginRight: 8 }} /> : null}
      <Ionicons name="chevron-forward" size={18} color={C.faint} />
    </Press>
  );
}

const styles = StyleSheet.create({
  me: { flexDirection: 'row', alignItems: 'center', backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  avatar: { width: 58, height: 58, borderRadius: 29, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  welcome: { borderRadius: R.lg, padding: 20 },
  group: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  row: { flexDirection: 'row', alignItems: 'center', minHeight: 58, paddingHorizontal: 14, paddingVertical: 8 },
  rowLine: { borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line2 },
  rowIcon: { width: 38, height: 38, borderRadius: 19, backgroundColor: C.bg, alignItems: 'center', justifyContent: 'center' },
  footer: { marginTop: 28, backgroundColor: C.dark, borderRadius: R.lg, padding: 20 },
});
