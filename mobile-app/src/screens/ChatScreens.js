/* Live Chat - the website's chat (the round button at the bottom of every
   website page), as the app's Chat tab.

   The same three steps as the website: a visitor gives name, e-mail and
   telephone once ("Chat with our team"); a signed-in customer skips that when
   their account already has them; then the team, then the conversation.

   PHASE 1: built and working on the phone, not yet connected - a message stays
   here marked "not sent", and the screen says so. PHASE 2: the website's chat
   API (send, poll, upload) behind the same screens, plus pictures, voice
   notes and calls exactly as on the website. */
import { useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { Alert, FlatList, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, TextInput, View } from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import { useHeaderHeight } from '@react-navigation/elements';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';
import * as ImagePicker from 'expo-image-picker';

import { C, GRAD, MAX_FONT, R, lift, useUI } from '../theme';
import { LOGO } from '../api/static';
import { Button, IconButton, Img, Press, T } from '../components/base';
import { Notice, TabHeader, Wrap } from '../components/layout';
import { Field, valid } from '../components/form';
import { useChat } from '../store/chat';
import { useAccount } from '../store/account';

const TEAM = 'SBK Global Auto Trading';
const PREVIEW = 'App preview: the live chat connects to the website in the next step. Until then, messages stay on this phone and are not sent.';

/* Who is chatting: the chat's own details, else the signed-in account's. */
function useChatter() {
  const chat = useChat();
  const account = useAccount();
  const u = account.user;
  if (chat.me) return chat.me;
  if (u && u.name && u.email && u.phone) return { name: u.name, email: u.email, phone: u.phone };
  return null;
}

export function ChatScreen() {
  const nav = useNavigation();
  const route = useRoute();
  const chat = useChat();
  const me = useChatter();
  const last = chat.messages[chat.messages.length - 1];

  // "Ask about this vehicle" and friends arrive with a first message ready.
  useEffect(() => {
    if (route.params?.draft && me) {
      nav.navigate('ChatThread', { draft: route.params.draft, at: route.params.at });
      nav.setParams({ draft: undefined });
    }
  }, [route.params?.draft, route.params?.at, me, nav]);

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader
        title="Live Chat"
        sub={me ? 'Signed in as ' + me.name : TEAM}
        right={me && chat.me ? <IconButton name="log-out-outline" onPress={() => Alert.alert('Leave chat', 'Forget your chat details on this phone?', [{ text: 'Cancel', style: 'cancel' }, { text: 'Leave', style: 'destructive', onPress: () => { chat.setMe(null); chat.clear(); } }])} label="Leave chat" /> : null}
      />
      {me ? (
        <ScrollView contentContainerStyle={{ paddingTop: 16, paddingBottom: 28 }}>
          <Wrap>
            <Notice tone="preview" text={PREVIEW} style={{ marginBottom: 16 }} />
            <T v="label" color={C.muted} style={{ marginBottom: 8, marginLeft: 4 }}>Our team</T>
            <Press onPress={() => nav.navigate('ChatThread')} style={styles.person} accessibilityLabel={'Chat with ' + TEAM}>
              <View style={styles.teamAvatar}>
                <Img src={LOGO} plain fit="contain" style={{ width: 44, height: 18 }} />
              </View>
              <View style={{ flex: 1, marginLeft: 12 }}>
                <T v="title" lines={1}>{TEAM}</T>
                <T v="small" color={C.muted} weight="500" lines={1} style={{ marginTop: 2 }}>
                  {last ? (last.kind === 'image' ? 'Picture' : last.text) : 'Sales and export support'}
                </T>
              </View>
              <Ionicons name="chevron-forward" size={18} color={C.faint} />
            </Press>
            <View style={styles.tips}>
              <T v="title">Talk to SBK about</T>
              {['A vehicle in the shop - price, condition, photos', 'Sourcing a car from Japanese auctions', 'Shipping, documents and payment steps'].map((t) => (
                <View key={t} style={styles.tip}>
                  <Ionicons name="checkmark-circle" size={17} color={C.red} />
                  <T v="sub" color={C.ink2} style={{ marginLeft: 8, flex: 1 }}>{t}</T>
                </View>
              ))}
              <Button title="Start Chatting" icon="chatbubbles-outline" onPress={() => nav.navigate('ChatThread')} full style={{ marginTop: 14 }} />
            </View>
          </Wrap>
        </ScrollView>
      ) : (
        <Gate onStart={(details) => {
          chat.setMe(details);
          if (route.params?.draft) {
            nav.navigate('ChatThread', { draft: route.params.draft, at: Date.now() });
            nav.setParams({ draft: undefined });
          }
        }}
        />
      )}
    </View>
  );
}

/* The website's chat gate (resources/views/chat/start.blade.php). */
function Gate({ onStart }) {
  const nav = useNavigation();
  const account = useAccount();
  const u = account.user;
  const [f, setF] = useState({ name: u ? u.name : '', email: u ? u.email : '', phone: u ? u.phone : '' });
  const [err, setErr] = useState('');
  const set = (k) => (v) => setF((x) => ({ ...x, [k]: v }));

  const start = () => {
    if (!valid.filled(f.name)) return setErr('Please tell us your name.');
    if (!valid.email(f.email)) return setErr('Please enter a valid e-mail address.');
    if (String(f.phone).replace(/[^0-9]/g, '').length < 6) return setErr('Please enter your telephone number.');
    setErr('');
    return onStart(f);
  };

  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingVertical: 20 }}>
        <Wrap>
          <View style={styles.gate}>
            <LinearGradient colors={GRAD.dark} style={styles.gateHead}>
              <Img src={LOGO} plain fit="contain" style={{ width: 150, height: 52 }} />
              <T v="h2" color={C.white} center style={{ marginTop: 10 }}>Chat with our team</T>
              <T v="sub" color="rgba(255,255,255,0.75)" center style={{ marginTop: 6 }}>Tell us who you are and we will put you straight through.</T>
            </LinearGradient>
            <View style={{ padding: 18 }}>
              {err ? <Notice tone="error" text={err} style={{ marginBottom: 14 }} /> : null}
              <Field label="Your name" value={f.name} onChangeText={set('name')} placeholder="e.g. Muhammad Yaseen" autoComplete="name" maxLength={120} />
              <Field label="E-mail" value={f.email} onChangeText={set('email')} placeholder="you@example.com" keyboardType="email-address" autoComplete="email" maxLength={160} editable={!u} />
              <Field label="Telephone" value={f.phone} onChangeText={set('phone')} placeholder="+92 300 0000000" keyboardType="phone-pad" autoComplete="tel" maxLength={40} returnKeyType="go" onSubmitEditing={start} />
              <Button title="Start chatting" iconRight="arrow-forward" onPress={start} full />
              <T v="small" color={C.muted} weight="500" center style={{ marginTop: 14 }}>We use these only to reply to you.</T>
              {u ? (
                <T v="small" color={C.muted} weight="500" center style={{ marginTop: 4 }}>Signed in as {u.email}.</T>
              ) : (
                <Press onPress={() => nav.navigate('Login')} style={{ minHeight: 40, justifyContent: 'center' }} accessibilityLabel="Sign in">
                  <T v="small" color={C.muted} weight="500" center>
                    Already have an SBK account? <T v="small" color={C.red} weight="800">Sign in</T>
                  </T>
                </Press>
              )}
            </View>
          </View>
        </Wrap>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

/* ----------------------------------------------------------- conversation */

export function ChatThreadScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const route = useRoute();
  const insets = useSafeAreaInsets();
  const headerH = useHeaderHeight();
  const chat = useChat();
  const [text, setText] = useState(route.params?.draft || '');

  useEffect(() => {
    if (route.params?.draft) setText(route.params.draft);
  }, [route.params?.draft, route.params?.at]);

  const soon = (what) => Alert.alert('App preview', what + ' switch on with the live chat, when the app is connected to the website in the next step.');

  useLayoutEffect(() => {
    nav.setOptions({
      headerTitle: () => (
        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
          <View style={[styles.teamAvatar, { width: 36, height: 36 }]}>
            <Img src={LOGO} plain fit="contain" style={{ width: 30, height: 12 }} />
          </View>
          <View style={{ marginLeft: 10 }}>
            <T v="title" lines={1}>{TEAM}</T>
            <T v="small" size={11} color={C.muted} weight="600">Preview - not connected yet</T>
          </View>
        </View>
      ),
      headerRight: () => (
        <View style={{ flexDirection: 'row' }}>
          <IconButton name="call-outline" onPress={() => soon('Voice calls')} label="Voice call" />
          <IconButton name="videocam-outline" onPress={() => soon('Video calls')} label="Video call" />
        </View>
      ),
    });
  }, [nav]);

  const rows = useMemo(() => withDays(chat.messages).reverse(), [chat.messages]);

  const send = () => {
    if (!text.trim()) return;
    chat.send(text);
    setText('');
  };

  const pickPicture = async () => {
    const r = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ['images'], quality: 0.7 });
    if (!r.canceled && r.assets && r.assets[0]) chat.sendPicture(r.assets[0].uri, r.assets[0].width, r.assets[0].height);
  };

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: '#ECEEF2' }} behavior={Platform.OS === 'ios' ? 'padding' : undefined} keyboardVerticalOffset={Platform.OS === 'ios' ? headerH : 0}>
      <FlatList
        data={rows}
        inverted
        keyExtractor={(m) => m.id}
        contentContainerStyle={{ paddingHorizontal: ui.gutter, paddingVertical: 12, flexGrow: 1 }}
        renderItem={({ item }) => (item.day ? <Day text={item.day} /> : <Bubble m={item} maxW={Math.min(ui.width * 0.78, 520)} />)}
        ListFooterComponent={(
          <View>
            <Notice tone="preview" text={PREVIEW} style={{ marginBottom: 10 }} />
          </View>
        )}
        ListEmptyComponent={(
          <View style={styles.hello}>
            <View style={styles.helloIcon}>
              <Ionicons name="hand-left-outline" size={26} color={C.red} />
            </View>
            <T v="h3" center>Say hello</T>
            <T v="sub" color={C.muted} center style={{ marginTop: 6 }}>This is the beginning of your conversation with {TEAM}.</T>
          </View>
        )}
        keyboardShouldPersistTaps="handled"
      />
      <View style={[styles.compose, { paddingBottom: Math.max(insets.bottom, 8) }]}>
        <IconButton name="image-outline" onPress={pickPicture} label="Send a picture" color={C.ink2} />
        <View style={styles.inputBox}>
          <TextInput
            value={text}
            onChangeText={setText}
            placeholder="Write a message..."
            placeholderTextColor={C.faint}
            multiline
            style={[styles.input, { fontSize: ui.t(15) }]}
            maxFontSizeMultiplier={MAX_FONT}
            selectionColor={C.red}
            cursorColor={C.red}
          />
        </View>
        {text.trim() ? (
          <Press onPress={send} style={styles.send} ripple="rgba(255,255,255,0.3)" accessibilityLabel="Send">
            <Ionicons name="send" size={19} color={C.white} style={{ marginLeft: 2 }} />
          </Press>
        ) : (
          <Press onPress={() => soon('Voice notes')} style={[styles.send, { backgroundColor: C.ink }]} ripple="rgba(255,255,255,0.3)" accessibilityLabel="Record a voice note">
            <Ionicons name="mic" size={20} color={C.white} />
          </Press>
        )}
      </View>
    </KeyboardAvoidingView>
  );
}

/* Messages with a day line wherever the date changes. */
function withDays(messages) {
  const out = [];
  let lastDay = '';
  messages.forEach((m) => {
    const d = new Date(m.at);
    const day = dayLabel(d);
    if (day !== lastDay) {
      out.push({ id: 'day-' + m.id, day });
      lastDay = day;
    }
    out.push(m);
  });
  return out;
}

function dayLabel(d) {
  const today = new Date();
  const y = new Date(Date.now() - 86400000);
  const same = (a, b) => a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  if (same(d, today)) return 'Today';
  if (same(d, y)) return 'Yesterday';
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

function clock(iso) {
  const d = new Date(iso);
  let h = d.getHours();
  const m = String(d.getMinutes()).padStart(2, '0');
  const ap = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return h + ':' + m + ' ' + ap;
}

function Day({ text }) {
  return (
    <View style={styles.day}>
      <T v="small" size={11} weight="700" color={C.muted}>{text}</T>
    </View>
  );
}

function Bubble({ m, maxW }) {
  const mine = m.mine;
  const w = m.kind === 'image' ? Math.min(maxW, 240) : undefined;
  const h = m.kind === 'image' && m.width && m.height ? Math.round(w * Math.min(1.4, m.height / m.width)) : 180;
  return (
    <View style={[styles.bubbleRow, mine ? { justifyContent: 'flex-end' } : null]}>
      <View style={[styles.bubble, mine ? styles.mine : styles.theirs, { maxWidth: maxW }, m.kind === 'image' && { padding: 4 }]}>
        {m.kind === 'image' ? (
          <Img src={{ uri: m.uri }} style={{ width: w, height: h, borderRadius: R.md }} />
        ) : (
          <T v="body" color={mine ? C.white : C.ink} selectable>{m.text}</T>
        )}
        <View style={[styles.stamp, m.kind === 'image' && { paddingHorizontal: 6, paddingBottom: 2 }]}>
          <T v="small" size={10.5} weight="500" color={mine ? 'rgba(255,255,255,0.78)' : C.faint}>{clock(m.at)}</T>
          {mine ? (
            <View style={{ flexDirection: 'row', alignItems: 'center', marginLeft: 5 }}>
              <Ionicons name="time-outline" size={12} color="rgba(255,255,255,0.85)" />
              <T v="small" size={10.5} weight="600" color="rgba(255,255,255,0.85)" style={{ marginLeft: 3 }}>Not sent</T>
            </View>
          ) : null}
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  person: { flexDirection: 'row', alignItems: 'center', backgroundColor: C.card, borderRadius: R.lg, padding: 14, ...lift },
  teamAvatar: { width: 50, height: 50, borderRadius: 25, backgroundColor: C.white, borderWidth: 1, borderColor: C.line, alignItems: 'center', justifyContent: 'center' },
  tips: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, marginTop: 14, ...lift },
  tip: { flexDirection: 'row', alignItems: 'center', marginTop: 10 },
  gate: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  gateHead: { alignItems: 'center', paddingVertical: 22, paddingHorizontal: 18 },
  hello: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingVertical: 40 },
  helloIcon: { width: 60, height: 60, borderRadius: 30, backgroundColor: C.redWash, alignItems: 'center', justifyContent: 'center', marginBottom: 12 },
  day: { alignSelf: 'center', backgroundColor: 'rgba(255,255,255,0.85)', borderRadius: R.pill, paddingHorizontal: 12, paddingVertical: 4, marginVertical: 10 },
  bubbleRow: { flexDirection: 'row', marginVertical: 3 },
  bubble: { borderRadius: 16, paddingHorizontal: 12, paddingTop: 8, paddingBottom: 6 },
  mine: { backgroundColor: C.red, borderBottomRightRadius: 4 },
  theirs: { backgroundColor: C.card, borderBottomLeftRadius: 4 },
  stamp: { flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end', marginTop: 3 },
  compose: { flexDirection: 'row', alignItems: 'flex-end', backgroundColor: C.card, paddingHorizontal: 8, paddingTop: 8, borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: C.line },
  inputBox: { flex: 1, backgroundColor: C.bg, borderRadius: 22, borderWidth: 1, borderColor: C.line, marginHorizontal: 6, minHeight: 44, justifyContent: 'center' },
  input: { color: C.ink, paddingHorizontal: 14, paddingTop: 10, paddingBottom: 10, maxHeight: 120 },
  send: { width: 44, height: 44, borderRadius: 22, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
});
