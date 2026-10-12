/* Live Chat - the website's own chat (the round button on every website page)
   as the app's Chat tab, on the same conversations the desk answers.

   The website's three steps: a visitor gives name, e-mail and telephone once
   ("Chat with our team"), a signed-in customer only what their account lacks;
   then the team, with who is online; then the conversation - text, pictures,
   voice notes and voice/video calls, with the desk's ticks. */
import { useCallback, useEffect, useLayoutEffect, useMemo, useRef, useState } from 'react';
import { ActivityIndicator, Alert, FlatList, KeyboardAvoidingView, Platform, RefreshControl, ScrollView, StyleSheet, TextInput, View } from 'react-native';
import { useFocusEffect, useNavigation, useRoute } from '@react-navigation/native';
import { useHeaderHeight } from '@react-navigation/elements';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, MAX_FONT, R, lift, useUI } from '../theme';
import { LOGO } from '../api/static';
import * as chatApi from '../api/chat';
import { mediaSource } from '../api/http';
import { Badge, Button, IconButton, Img, Press, T } from '../components/base';
import { Empty, Notice, TabHeader, Wrap } from '../components/layout';
import { Field, valid } from '../components/form';
import VoiceBubble from '../components/VoiceBubble';
import { useChat } from '../store/chat';
import { useAccount } from '../store/account';
import { audioLib, pickerLib } from '../lazy';
import { useCallEngine, useCallSnap } from '../calls';
import { openSite } from '../actions';

const FACES = ['#D71920', '#111216', '#A90F15', '#3A3F47', '#7E0A0F', '#2A2D35'];
const MAX_VOICE_SECS = 300;

function Face({ name, ini, hue, size = 46, online }) {
  return (
    <View style={{ width: size, height: size }}>
      <View style={[styles.face, { width: size, height: size, borderRadius: size / 2, backgroundColor: FACES[Math.abs(Number(hue) || 0) % FACES.length] }]}>
        <T v="title" size={size * 0.34} color={C.white}>{ini || String(name || '?').slice(0, 2).toUpperCase()}</T>
      </View>
      {online ? <View style={styles.dot} /> : null}
    </View>
  );
}

/* ================================================================ the tab */

export function ChatScreen() {
  const nav = useNavigation();
  const route = useRoute();
  const chat = useChat();
  const account = useAccount();

  // "Ask about this vehicle" and the like arrive with a first message ready.
  useEffect(() => {
    if (route.params?.draft) {
      chat.setDraft(route.params.draft);
      nav.setParams({ draft: undefined });
    }
  }, [route.params?.draft, route.params?.at]); // eslint-disable-line react-hooks/exhaustive-deps

  const w = chat.who;
  const leave = () => Alert.alert('Leave chat', 'Leave the chat on this phone? Your conversation stays with SBK.', [
    { text: 'Cancel', style: 'cancel' },
    { text: 'Leave', style: 'destructive', onPress: () => chat.leave() },
  ]);

  let body;
  if (!chat.ready) {
    body = <ActivityIndicator color={C.red} style={{ marginTop: 60 }} />;
  } else if (!w || w.details) {
    body = <Gate details={!!(w && w.details)} me={w ? w.me : null} />;
  } else if (w.staff) {
    body = (
      <Wrap style={{ paddingTop: 20 }}>
        <Notice tone="info" title="This is a staff account" text="Customers' conversations are answered from the chat desk on the website." />
        <Button title="Open the Chat Desk" icon="open-outline" kind="dark" onPress={() => openSite('/chat')} style={{ marginTop: 14 }} />
      </Wrap>
    );
  } else {
    body = <Team />;
  }

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader
        title="Live Chat"
        sub={w && w.me && w.me.name ? 'Signed in as ' + w.me.name : 'SBK Global Auto Trading'}
        right={w && !account.user ? <IconButton name="log-out-outline" onPress={leave} label="Leave chat" /> : null}
      />
      {body}
    </View>
  );
}

/* The website's chat gate (resources/views/chat/start.blade.php). */
function Gate({ details, me }) {
  const nav = useNavigation();
  const chat = useChat();
  const account = useAccount();
  const u = account.user;
  const [f, setF] = useState({
    name: (me && me.name && !String(me.name).includes('@') ? me.name : '') || (u ? u.name : ''),
    email: (me && me.email) || (u ? u.email : ''),
    phone: (me && me.phone) || (u ? u.phone : ''),
  });
  const [err, setErr] = useState('');
  const [busy, setBusy] = useState(false);
  const set = (k) => (v) => setF((x) => ({ ...x, [k]: v }));
  const fixedEmail = details || !!u;

  const start = async () => {
    if (String(f.name).trim().length < 2) return setErr('Please tell us your name.');
    if (!valid.email(f.email)) return setErr('That e-mail address does not look right.');
    if (String(f.phone).replace(/[^0-9]/g, '').length < 7) return setErr('Please give a telephone number we can reach you on.');
    setErr('');
    setBusy(true);
    try {
      await chat.start(fixedEmail ? { name: f.name, phone: f.phone } : f);
    } catch (e) {
      setErr(e.message);
    } finally {
      setBusy(false);
    }
    return null;
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
              <Field label="E-mail" value={f.email} onChangeText={set('email')} placeholder="you@example.com" keyboardType="email-address" autoComplete="email" maxLength={160} editable={!fixedEmail} />
              <Field label="Telephone" value={f.phone} onChangeText={set('phone')} placeholder="+92 300 0000000" keyboardType="phone-pad" autoComplete="tel" maxLength={40} returnKeyType="go" onSubmitEditing={start} />
              <Button title="Start chatting" iconRight="arrow-forward" onPress={start} loading={busy} full />
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

/* The team: who a customer can write to, online first (the website's list). */
function Team() {
  const nav = useNavigation();
  const chat = useChat();
  const calls = useCallEngine();
  const [state, setState] = useState(null);
  const [error, setError] = useState('');
  const [refreshing, setRefreshing] = useState(false);

  const load = useCallback(async () => {
    try {
      const r = await chatApi.poll({ me: 1 });
      setState(r);
      setError('');
      chat.setUnread(r.unread || 0);
      if (r.call && calls) calls.notePoll(r.call);
    } catch (e) {
      if (e.status === 401) chat.refreshWho();
      else setError(e.message);
    }
  }, [chat, calls]);

  useFocusEffect(useCallback(() => {
    load();
    const t = setInterval(load, 5000);
    return () => clearInterval(t);
  }, [load]));

  const people = state ? state.people || [] : [];
  const threads = state ? state.threads || {} : {};

  return (
    <ScrollView
      contentContainerStyle={{ paddingTop: 16, paddingBottom: 28 }}
      refreshControl={<RefreshControl refreshing={refreshing} onRefresh={async () => { setRefreshing(true); await load(); setRefreshing(false); }} colors={[C.red]} tintColor={C.red} />}
    >
      <Wrap>
        {chat.draft ? <Notice tone="info" title="Your question is ready" text="Choose someone from our team to send it to." style={{ marginBottom: 14 }} /> : null}
        {error ? <Notice tone="error" text={error} style={{ marginBottom: 14 }} /> : null}
        <T v="label" color={C.muted} style={{ marginBottom: 8, marginLeft: 4 }}>Our team</T>
        {!state && !error ? <ActivityIndicator color={C.red} style={{ marginTop: 30 }} /> : null}
        {state && !people.length ? <Empty icon="people-outline" title="Nobody is listed yet" text="Our team will appear here." /> : null}
        {people.length ? (
          <View style={styles.list}>
            {people.map((p, i) => {
              const t = threads[String(p.id)] || null;
              return (
                <Press
                  key={p.id}
                  onPress={() => nav.navigate('ChatThread', { staffId: p.id, threadId: t ? t.thread : null, name: p.name, role: p.role, online: p.online, ini: p.ini, hue: p.hue })}
                  style={[styles.person, i < people.length - 1 && styles.personLine]}
                  accessibilityLabel={'Chat with ' + p.name}
                >
                  <Face name={p.name} ini={p.ini} hue={p.hue} online={p.online} />
                  <View style={{ flex: 1, marginLeft: 12 }}>
                    <T v="title" lines={1}>{p.name}</T>
                    <T v="small" weight="600" color={p.online ? C.ok : C.muted} lines={1} style={{ marginTop: 2 }}>{p.online ? 'Online now' : (p.role || 'Offline')}</T>
                  </View>
                  {t && t.unread ? <Badge count={t.unread} style={{ position: 'relative', marginRight: 8 }} /> : null}
                  <Ionicons name="chevron-forward" size={18} color={C.faint} />
                </Press>
              );
            })}
          </View>
        ) : null}
        <T v="small" color={C.muted} weight="500" center style={{ marginTop: 14 }}>Anyone with a green dot is at their desk right now.</T>
      </Wrap>
    </ScrollView>
  );
}

/* =========================================================== conversation */

export function ChatThreadScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const route = useRoute();
  const insets = useSafeAreaInsets();
  const headerH = useHeaderHeight();
  const chat = useChat();
  const calls = useCallEngine();
  const callSnap = useCallSnap();
  const p = route.params || {};
  const [threadId, setThreadId] = useState(p.threadId || null);
  const [messages, setMessages] = useState([]);
  const [loading, setLoading] = useState(!!p.threadId);
  const [text, setText] = useState('');
  const [busy, setBusy] = useState(false);
  const [err, setErr] = useState('');
  const after = useRef(0);

  // A question carried in from a vehicle page is waiting in the box.
  useEffect(() => {
    if (chat.draft) {
      setText(chat.draft);
      chat.setDraft('');
    }
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  useLayoutEffect(() => {
    nav.setOptions({
      headerTitle: () => (
        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
          <Face name={p.name} ini={p.ini} hue={p.hue} size={36} online={p.online} />
          <View style={{ marginLeft: 10, maxWidth: ui.width - 150 }}>
            <T v="title" lines={1}>{p.name}</T>
            <T v="small" size={11} weight="600" color={p.online ? C.ok : C.muted} lines={1}>{p.online ? 'Online now' : (p.role || 'SBK team')}</T>
          </View>
        </View>
      ),
      // Calls, as on the website: to the member of the team this conversation is with.
      headerRight: () => (calls ? (
        <View style={{ flexDirection: 'row' }}>
          <IconButton name="call-outline" onPress={() => startCall('voice')} label="Voice call" />
          <IconButton name="videocam-outline" onPress={() => startCall('video')} label="Video call" />
        </View>
      ) : null),
    });
  }, [nav, p.name, p.online, p.role, p.ini, p.hue, ui.width, calls, callSnap.status]); // eslint-disable-line react-hooks/exhaustive-deps

  const startCall = (kind) => {
    if (!calls) return;
    if (calls.busy()) { setErr('You are already in a call.'); return; }
    calls.start(kind, { to: p.staffId, peer: p.name || 'SBK', peerId: p.staffId, hue: p.hue });
  };

  const merge = useCallback((incoming, receipts) => {
    setMessages((prev) => {
      const byId = new Map(prev.map((m) => [m.id, m]));
      (incoming || []).forEach((m) => byId.set(m.id, m));
      (receipts || []).forEach((r) => {
        const m = byId.get(r.id);
        if (m) byId.set(r.id, { ...m, delivered: !!r.d, read: !!r.r });
      });
      const list = [...byId.values()].sort((a, b) => a.id - b.id);
      if (list.length) after.current = list[list.length - 1].id;
      return list;
    });
  }, []);

  // No conversation yet: the first message or call makes one - a call says which.
  useEffect(() => {
    if (!threadId && callSnap.thread && Number(callSnap.peerId) === Number(p.staffId)) setThreadId(callSnap.thread);
  }, [threadId, callSnap.thread, callSnap.peerId, p.staffId]);

  const poll = useCallback(async () => {
    if (!threadId) {
      setLoading(false);
      return;
    }
    try {
      const r = await chatApi.poll({ thread: threadId, after: after.current, open: 1 });
      merge(r.messages, r.receipts);
      if (r.call && calls) calls.notePoll(r.call);
      if (typeof r.unread === 'number') chat.setUnread(r.unread);
      setErr('');
    } catch (e) {
      if (e.status === 401) { chat.refreshWho(); nav.goBack(); }
    } finally {
      setLoading(false);
    }
  }, [threadId, merge, chat, nav, calls, p.staffId]);

  useFocusEffect(useCallback(() => {
    poll();
    const t = setInterval(poll, 2500);
    return () => clearInterval(t);
  }, [poll]));

  const sent = (r) => {
    if (r.thread && r.thread !== threadId) setThreadId(r.thread);
    if (r.message) merge([r.message]);
  };

  const send = async () => {
    const body = text.trim();
    if (!body || busy) return;
    setBusy(true);
    try {
      sent(await chatApi.send(threadId ? { thread: threadId, body } : { to: p.staffId, body }));
      setText('');
      setErr('');
    } catch (e) {
      setErr(e.message);
    } finally {
      setBusy(false);
    }
  };

  const sendFile = async (file) => {
    setBusy(true);
    try {
      sent(await chatApi.upload({ ...(threadId ? { thread: threadId } : { to: p.staffId }), ...file }));
      setErr('');
    } catch (e) {
      setErr(e.message);
    } finally {
      setBusy(false);
    }
  };

  const pickPicture = async () => {
    const IP = pickerLib();
    if (!IP) return setErr('Pictures cannot be sent from this phone.');
    const r = await IP.launchImageLibraryAsync({ mediaTypes: ['images'], quality: 0.8 });
    if (!r.canceled && r.assets && r.assets[0]) {
      const a = r.assets[0];
      await sendFile({ kind: 'image', uri: a.uri, mimeType: a.mimeType || 'image/jpeg', fileName: a.fileName || 'photo.jpg' });
    }
    return null;
  };

  /* --------------------------------------------------------- voice notes */
  const A = audioLib();
  const recorder = A ? A.useAudioRecorder(A.RecordingPresets.HIGH_QUALITY) : null;
  const [recSecs, setRecSecs] = useState(0);
  const [recording, setRecording] = useState(false);
  const recOn = useRef(false);
  const recTimer = useRef(null);

  useEffect(() => () => {
    if (recTimer.current) clearInterval(recTimer.current);
    if (recOn.current && recorder) { try { recorder.stop(); } catch (e) { /* */ } }
  }, [recorder]);

  const startRecording = async () => {
    if (busy || recOn.current) return;
    if (!A || !recorder) { setErr('Voice notes cannot be recorded on this phone.'); return; }
    try {
      const perm = await A.requestRecordingPermissionsAsync();
      if (!perm.granted) {
        Alert.alert('No access to the microphone', 'Allow microphone access in Settings to send a voice note.');
        return;
      }
      await A.setAudioModeAsync({ allowsRecording: true, playsInSilentMode: true });
      await recorder.prepareToRecordAsync();
      recorder.record();
      recOn.current = true;
      setRecSecs(0);
      setRecording(true);
      recTimer.current = setInterval(() => setRecSecs((n) => n + 1), 1000);
    } catch (e) {
      recOn.current = false;
      setErr('Could not start recording.');
    }
  };

  const stopRecorder = async () => {
    if (recTimer.current) { clearInterval(recTimer.current); recTimer.current = null; }
    setRecording(false);
    if (!recOn.current || !recorder) return null;
    recOn.current = false;
    try { await recorder.stop(); } catch (e) { /* */ }
    try { await A.setAudioModeAsync({ allowsRecording: false, playsInSilentMode: true }); } catch (e) { /* */ }
    return recorder.uri;
  };

  const finishRecording = async () => {
    const secs = recSecs;
    const uri = await stopRecorder();
    setRecSecs(0);
    if (secs < 1) { setErr('That was too short - hold on a moment longer.'); return; }
    if (!uri) { setErr('The recording could not be saved on this phone.'); return; }
    await sendFile({ kind: 'voice', uri, secs, mimeType: 'audio/mp4', fileName: 'voice.m4a' });
  };

  // Five minutes and the recording sends itself, as the website's does.
  useEffect(() => {
    if (recording && recSecs >= MAX_VOICE_SECS) finishRecording();
  }, [recSecs, recording]); // eslint-disable-line react-hooks/exhaustive-deps

  const rows = useMemo(() => withDays(messages).reverse(), [messages]);
  const maxW = Math.min(ui.width * 0.78, 520);
  const openPicture = (m) => nav.navigate('Gallery', { images: [mediaSource(m.id)], index: 0, title: p.name });

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: '#ECEEF2' }} behavior={Platform.OS === 'ios' ? 'padding' : undefined} keyboardVerticalOffset={Platform.OS === 'ios' ? headerH : 0}>
      <FlatList
        data={rows}
        inverted
        keyExtractor={(m) => String(m.id)}
        contentContainerStyle={{ paddingHorizontal: ui.gutter, paddingVertical: 12, flexGrow: 1 }}
        renderItem={({ item }) => (item.day ? <Day text={item.day} /> : <Bubble m={item} maxW={maxW} onPicture={openPicture} />)}
        ListEmptyComponent={loading ? <ActivityIndicator color={C.red} style={{ marginTop: 40 }} /> : (
          <View style={styles.hello}>
            <View style={styles.helloIcon}>
              <Ionicons name="hand-left-outline" size={26} color={C.red} />
            </View>
            <T v="h3" center>Say hello</T>
            <T v="sub" color={C.muted} center style={{ marginTop: 6 }}>This is the beginning of your conversation with {p.name || 'us'}.</T>
          </View>
        )}
        keyboardShouldPersistTaps="handled"
      />
      {err ? <Notice tone="error" text={err} style={{ marginHorizontal: ui.gutter, marginBottom: 8 }} /> : null}
      {recording ? (
        <View style={[styles.compose, { paddingBottom: Math.max(insets.bottom, 8) }]}>
          <IconButton name="trash-outline" onPress={async () => { await stopRecorder(); setRecSecs(0); }} label="Cancel voice note" color={C.ink2} />
          <View style={styles.recBox}>
            <View style={styles.recDot} />
            <T v="title" color={C.red} style={{ marginLeft: 8 }}>{Math.floor(recSecs / 60)}:{String(recSecs % 60).padStart(2, '0')}</T>
            <T v="small" color={C.muted} weight="500" style={{ marginLeft: 10 }} lines={1}>Recording...</T>
          </View>
          <Press onPress={finishRecording} style={styles.send} ripple="rgba(255,255,255,0.3)" accessibilityLabel="Send voice note">
            <Ionicons name="send" size={19} color={C.white} style={{ marginLeft: 2 }} />
          </Press>
        </View>
      ) : (
        <View style={[styles.compose, { paddingBottom: Math.max(insets.bottom, 8) }]}>
          <IconButton name="image-outline" onPress={pickPicture} label="Send a picture" color={C.ink2} />
          <View style={styles.inputBox}>
            <TextInput
              value={text}
              onChangeText={setText}
              placeholder="Write a message..."
              placeholderTextColor={C.faint}
              multiline
              maxLength={4000}
              style={[styles.input, { fontSize: ui.t(15) }]}
              maxFontSizeMultiplier={MAX_FONT}
              selectionColor={C.red}
              cursorColor={C.red}
            />
          </View>
          {busy ? (
            <View style={[styles.send, { backgroundColor: C.line }]}><ActivityIndicator color={C.red} /></View>
          ) : text.trim() ? (
            <Press onPress={send} style={styles.send} ripple="rgba(255,255,255,0.3)" accessibilityLabel="Send">
              <Ionicons name="send" size={19} color={C.white} style={{ marginLeft: 2 }} />
            </Press>
          ) : (
            <Press onPress={startRecording} style={[styles.send, { backgroundColor: C.ink }]} ripple="rgba(255,255,255,0.3)" accessibilityLabel="Record a voice note">
              <Ionicons name="mic" size={20} color={C.white} />
            </Press>
          )}
        </View>
      )}
    </KeyboardAvoidingView>
  );
}

/* Messages with a day line wherever the date changes. */
function withDays(messages) {
  const out = [];
  let lastDay = '';
  messages.forEach((m) => {
    const day = dayLabel(atOf(m.at));
    if (day !== lastDay) {
      out.push({ id: 'day-' + m.id, day });
      lastDay = day;
    }
    out.push(m);
  });
  return out;
}

/* The website writes times as UTC 'Y-m-d H:i:s'. */
function atOf(at) {
  const s = String(at || '');
  const d = new Date(s.includes('T') ? s : s.replace(' ', 'T') + 'Z');
  return isNaN(d.getTime()) ? new Date() : d;
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

function clock(at) {
  const d = atOf(at);
  let h = d.getHours();
  const m = String(d.getMinutes()).padStart(2, '0');
  const ap = h >= 12 ? 'PM' : 'AM';
  h = h % 12 || 12;
  return h + ':' + m + ' ' + ap;
}

function dur(s) {
  const v = Math.max(0, s | 0);
  return Math.floor(v / 60) + ':' + String(v % 60).padStart(2, '0');
}

function Day({ text }) {
  return (
    <View style={styles.day}>
      <T v="small" size={11} weight="700" color={C.muted}>{text}</T>
    </View>
  );
}

/* A call line, in the website chat's words (chat.js callLogHtml). */
function callLine(m) {
  const b = String(m.body || '').split('|');
  const video = b[0] === 'video';
  const how = b[1] || '';
  const what = video ? 'Video call' : 'Voice call';
  let line = what;
  let sub = '';
  if (how === 'ended') sub = dur(parseInt(b[2] || '0', 10));
  if (how === 'missed') line = m.mine ? what + ' - no answer' : 'Missed ' + what.toLowerCase();
  if (how === 'cancelled') line = m.mine ? what + ' - cancelled' : 'Missed ' + what.toLowerCase();
  if (how === 'declined') line = m.mine ? what + ' - declined' : what + ' - you declined';
  if (how === 'failed') line = what + ' - could not connect';
  return { line, sub, video };
}

function Ticks({ m }) {
  if (!m.mine) return null;
  const name = m.delivered || m.read ? 'checkmark-done' : 'checkmark';
  return <Ionicons name={name} size={14} color={m.read ? '#8FD0FF' : 'rgba(255,255,255,0.85)'} style={{ marginLeft: 4 }} />;
}

function Bubble({ m, maxW, onPicture }) {
  const mine = m.mine;
  if (m.kind === 'call') {
    const c = callLine(m);
    return (
      <View style={styles.callRow}>
        <Ionicons name={c.video ? 'videocam-outline' : 'call-outline'} size={14} color={C.muted} />
        <T v="small" weight="700" color={C.ink2} style={{ marginLeft: 6 }}>{c.line}{c.sub ? ' (' + c.sub + ')' : ''}</T>
        <T v="small" size={10.5} weight="500" color={C.faint} style={{ marginLeft: 8 }}>{clock(m.at)}</T>
      </View>
    );
  }
  const picW = Math.min(maxW, 240);
  return (
    <View style={[styles.bubbleRow, mine ? { justifyContent: 'flex-end' } : null]}>
      <View style={[styles.bubble, mine ? styles.mine : styles.theirs, { maxWidth: maxW }, m.kind === 'image' && { padding: 4 }]}>
        {m.kind === 'image' ? (
          <Press onPress={() => onPicture(m)} accessibilityLabel="Open picture">
            <Img src={mediaSource(m.id)} style={{ width: picW, height: Math.round(picW * 0.75), borderRadius: R.md }} />
          </Press>
        ) : m.kind === 'voice' ? (
          <VoiceBubble source={mediaSource(m.id)} mine={mine} knownSecs={m.secs} />
        ) : (
          <T v="body" color={mine ? C.white : C.ink} selectable>{m.body}</T>
        )}
        <View style={[styles.stamp, m.kind === 'image' && { paddingHorizontal: 6, paddingBottom: 2 }]}>
          <T v="small" size={10.5} weight="500" color={mine ? 'rgba(255,255,255,0.78)' : C.faint}>{clock(m.at)}</T>
          <Ticks m={m} />
        </View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  face: { alignItems: 'center', justifyContent: 'center' },
  dot: { position: 'absolute', right: 0, bottom: 0, width: 13, height: 13, borderRadius: 7, backgroundColor: '#1FAF5A', borderWidth: 2, borderColor: C.white },
  list: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  person: { flexDirection: 'row', alignItems: 'center', padding: 14 },
  personLine: { borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line2 },
  gate: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  gateHead: { alignItems: 'center', paddingVertical: 22, paddingHorizontal: 18 },
  hello: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingVertical: 40 },
  helloIcon: { width: 60, height: 60, borderRadius: 30, backgroundColor: C.redWash, alignItems: 'center', justifyContent: 'center', marginBottom: 12 },
  day: { alignSelf: 'center', backgroundColor: 'rgba(255,255,255,0.85)', borderRadius: R.pill, paddingHorizontal: 12, paddingVertical: 4, marginVertical: 10 },
  callRow: { alignSelf: 'center', flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.85)', borderRadius: R.pill, paddingHorizontal: 12, paddingVertical: 6, marginVertical: 6 },
  bubbleRow: { flexDirection: 'row', marginVertical: 3 },
  bubble: { borderRadius: 16, paddingHorizontal: 12, paddingTop: 8, paddingBottom: 6 },
  mine: { backgroundColor: C.red, borderBottomRightRadius: 4 },
  theirs: { backgroundColor: C.card, borderBottomLeftRadius: 4 },
  stamp: { flexDirection: 'row', alignItems: 'center', justifyContent: 'flex-end', marginTop: 3 },
  compose: { flexDirection: 'row', alignItems: 'flex-end', backgroundColor: C.card, paddingHorizontal: 8, paddingTop: 8, borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: C.line },
  inputBox: { flex: 1, backgroundColor: C.bg, borderRadius: 22, borderWidth: 1, borderColor: C.line, marginHorizontal: 6, minHeight: 44, justifyContent: 'center' },
  input: { color: C.ink, paddingHorizontal: 14, paddingTop: 10, paddingBottom: 10, maxHeight: 120 },
  send: { width: 44, height: 44, borderRadius: 22, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  recBox: { flex: 1, flexDirection: 'row', alignItems: 'center', minHeight: 44, marginHorizontal: 6, paddingHorizontal: 14, borderRadius: 22, backgroundColor: C.redWash },
  recDot: { width: 10, height: 10, borderRadius: 5, backgroundColor: C.red },
});
