/* The website's live chat, from the app: the same endpoints the website's chat
   page uses (sbk-main routes/chat.php), under /api/app/chat with the app's
   token, so a message sent here is in the same conversation the desk sees. */
import { request } from './http';

/** Who the chat knows this phone as (a signed-in customer needs no details). */
export async function who() {
  return request('GET', '/chat/start', { chat: true });
}

/** The way in: name, e-mail and telephone, as on the website. */
export async function start(details) {
  return request('POST', '/chat/start', { chat: true, json: details });
}

/** Everything a screen needs in one call: the team, threads, new messages. */
export async function poll({ thread, after, open, me } = {}) {
  return request('GET', '/chat/poll', {
    chat: true,
    timeout: 15000,
    query: { thread: thread || undefined, after: after || undefined, open: open ? 1 : undefined, me: me ? 1 : undefined },
  });
}

/** A message to a member of the team (`to`, first message) or into a thread. */
export async function send({ to, thread, body }) {
  return request('POST', '/chat/send', { chat: true, json: thread ? { thread, body } : { to, body } });
}

/** A picture (kind 'image') or a voice note (kind 'voice', with its seconds). */
export async function upload({ to, thread, uri, kind, secs, mimeType, fileName }) {
  const form = new FormData();
  form.append('kind', kind);
  if (thread) form.append('thread', String(thread));
  else form.append('to', String(to));
  if (secs) form.append('secs', String(Math.round(secs)));
  form.append('file', {
    uri,
    name: fileName || (kind === 'voice' ? 'voice.m4a' : 'photo.jpg'),
    type: mimeType || (kind === 'voice' ? 'audio/mp4' : 'image/jpeg'),
  });
  return request('POST', '/chat/upload', { chat: true, form, timeout: 90000 });
}

export async function setName(name) {
  return request('POST', '/chat/name', { chat: true, json: { name } });
}

/** The website chat's lightweight beat: unread count and a call ringing for this person. */
export async function ping() {
  return request('GET', '/chat/ping', { chat: true, query: { from: 'chat' }, timeout: 10000 });
}

/* ---- calls: the website chat's own call endpoint (ChatCallController) */

/** Where two phones/browsers look for each other: STUN, and the relay when one is set up. */
export async function iceServers() {
  const r = await request('GET', '/chat/ice', { chat: true });
  return Array.isArray(r.iceServers) ? r.iceServers : [];
}

/** Ring a member of the team (`to`) - or, in a thread, its other side. */
export async function callStart({ kind, offer, thread = 0, to = 0 }) {
  const r = await request('POST', '/chat/call', { chat: true, urlencoded: { action: 'start', kind, offer, ...(to ? { to } : { thread }) } });
  return r.call;
}

export async function callState(id) {
  const r = await request('GET', '/chat/call', { chat: true, query: { action: 'state', call: id }, timeout: 10000 });
  return r.call;
}

export async function callAnswer(id, answer) {
  const r = await request('POST', '/chat/call', { chat: true, urlencoded: { action: 'answer', call: id, answer } });
  return r.call;
}

/** decline / cancel / end - the website works out which from the call's state. */
export async function callFinish(id, action, failed) {
  const r = await request('POST', '/chat/call', { chat: true, urlencoded: { action, call: id, ...(failed ? { failed: 1 } : {}) } });
  return r.call;
}
