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
