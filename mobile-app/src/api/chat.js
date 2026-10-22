/* The website's live chat, from the app: the same endpoints the website's chat
   page uses (sbk-main routes/chat.php), under /api/app/chat with the app's
   token, so a message sent here is in the same conversation the desk sees. */
import { SITE_URL } from '../config';
import { answer, chatHeaders, request } from './http';

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

/** A message to a member of the team. A customer always sends `to` - the website's
    send.php reads `to` for every customer; `thread` is only the desk's way in. */
export async function send({ to, thread, body }) {
  return request('POST', '/chat/send', { chat: true, json: thread ? { thread, body } : { to, body } });
}

/** expo-audio hands back a bare path on some phones; everything wants a scheme. */
function withScheme(uri) {
  const s = String(uri || '');
  if (!s || /^(file|content|https?):/i.test(s)) return s;
  return 'file://' + s;
}

let fsMod;
function fileSystem() {
  if (fsMod === undefined) {
    try {
      // eslint-disable-next-line global-require
      fsMod = require('expo-file-system');
    } catch (e) {
      fsMod = null;
    }
  }
  return fsMod;
}

/**
 * A picture (kind 'image') or a voice note (kind 'voice', with its seconds).
 *
 * Sent the way the SBK Chat app learned to on 18 Sep: React Native's FormData
 * has to open the file itself, and with a recording (a bare path, a file still
 * being written) it fails inside the phone as "no connection" - the request
 * never reaches the website. So the phone's own uploader streams the file from
 * disk with the same fields; FormData stays as the second chance.
 */
export async function upload({ to, thread, uri, kind, secs, mimeType, fileName }) {
  const file = withScheme(uri);
  const name = fileName || (kind === 'voice' ? 'voice.m4a' : 'photo.jpg');
  const type = mimeType || (kind === 'voice' ? 'audio/mp4' : 'image/jpeg');
  const fields = { kind };
  if (to) fields.to = String(to);
  else fields.thread = String(thread);
  if (kind === 'voice') fields.secs = String(Math.round(secs || 0));

  const F = fileSystem();
  if (F && F.File && F.UploadType) {
    let res = null;
    try {
      res = await new F.File(file).upload(SITE_URL + '/api/app/chat/upload', {
        httpMethod: 'POST',
        uploadType: F.UploadType.MULTIPART,
        fieldName: 'file',
        mimeType: type,
        parameters: fields,
        headers: chatHeaders(),
      });
    } catch (e) {
      res = null; // it could not go this way - FormData below gets its chance
    }
    if (res) {
      let data = null;
      try { data = JSON.parse(res.body); } catch (e) { data = null; }
      return answer(res.status, res.status >= 200 && res.status < 300, data);
    }
  }

  const form = new FormData();
  Object.keys(fields).forEach((k) => form.append(k, fields[k]));
  form.append('file', { uri: file, name, type });
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
