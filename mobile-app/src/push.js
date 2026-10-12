/* Notifications - the desk's replies and calls on the customer's phone when
   the app is closed (the SBK Chat app's push, proven on real phones).

   The website sends them through Firebase (App\Services\App\AppPush) as data
   messages expo-notifications turns into a notification; this file makes the
   two channels (Messages, Calls - two switches in the phone's settings), hands
   the website this phone's token for the customer in the chat, takes it back on
   leaving, and opens the conversation when a notification is tapped. While the
   app is open nothing pops up: the Chat tab's badge and the call screen speak. */
import { Platform } from 'react-native';
import { request } from './api/http';

let N;
function lib() {
  if (N === undefined) {
    try {
      // eslint-disable-next-line global-require
      N = require('expo-notifications');
    } catch (e) {
      N = null;
    }
  }
  return N;
}

let channelsMade = false;
let lastToken = null;

async function channels() {
  const n = lib();
  if (!n || channelsMade || Platform.OS !== 'android') return;
  channelsMade = true;
  await n.setNotificationChannelAsync('messages', {
    name: 'Messages',
    importance: n.AndroidImportance.HIGH,
    vibrationPattern: [0, 250, 150, 250],
    lightColor: '#D71920',
  });
  await n.setNotificationChannelAsync('calls', {
    name: 'Calls',
    importance: n.AndroidImportance.MAX,
    sound: 'ring.ogg',
    vibrationPattern: [0, 800, 1200, 800],
    lockscreenVisibility: n.AndroidNotificationVisibility.PUBLIC,
  });
}

/** Ask once, then hand the website this phone's token for the customer now in the chat. */
export async function startPush() {
  const n = lib();
  if (!n || Platform.OS === 'web') return false;
  try {
    n.setNotificationHandler({
      handleNotification: async () => ({ shouldShowBanner: false, shouldShowList: false, shouldPlaySound: false, shouldSetBadge: false }),
    });
    await channels();
    let perm = await n.getPermissionsAsync();
    if (!perm.granted && perm.canAskAgain !== false) perm = await n.requestPermissionsAsync();
    if (!perm.granted) return false;
    const t = await n.getDevicePushTokenAsync();
    if (!t || !t.data) return false;
    lastToken = String(t.data);
    await request('POST', '/chat/push', { chat: true, json: { token: lastToken, platform: Platform.OS } });
    return true;
  } catch (e) {
    return false;
  }
}

/** Leaving the chat or signing out: this phone stops hearing about that customer. */
export async function stopPush() {
  if (!lastToken) return;
  const token = lastToken;
  lastToken = null;
  try {
    await request('POST', '/chat/push', { chat: true, json: { token, do: 'forget' } });
  } catch (e) { /* the website forgets it anyway when Google says the app is gone */ }
}

/** A tapped notification: what it was about (the website's `body`). */
export function onTap(handler) {
  const n = lib();
  if (!n || Platform.OS === 'web') return () => {};
  const read = (resp) => {
    try {
      const data = resp && resp.notification && resp.notification.request.content.data;
      const body = data && typeof data.body === 'string' ? JSON.parse(data.body) : data;
      if (body && body.type) handler(body);
    } catch (e) { /* not ours */ }
  };
  // Opened by a tap while the app was closed.
  n.getLastNotificationResponseAsync().then(read).catch(() => {});
  const sub = n.addNotificationResponseReceivedListener(read);
  return () => sub.remove();
}
