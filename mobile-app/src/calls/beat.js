/* The chat's beat while the app is open: every few seconds the website's
   chat ping says how many replies are waiting (the Chat tab's badge) and
   whether a call is ringing for this person - which then comes up on the call
   screen over whatever is open, as on the website. Paused while the app is in
   the background. */
import { useEffect, useRef } from 'react';
import { AppState } from 'react-native';

import * as chatApi from '../api/chat';
import { useChat } from '../store/chat';
import { ringingAsCall, useCallEngine } from './index';

const EVERY = 6000;

export default function ChatBeat() {
  const chat = useChat();
  const engine = useCallEngine();
  const ref = useRef({ chat, engine });
  ref.current = { chat, engine };
  const customer = !!(chat.who && !chat.who.staff && !chat.who.details);

  useEffect(() => {
    if (!customer) return undefined;
    let timer = null;
    const tick = () => {
      chatApi.ping()
        .then((r) => {
          const { chat: c, engine: e } = ref.current;
          c.setUnread(r.unread || 0);
          const call = ringingAsCall(r.ringing);
          if (call && e) e.notePoll(call);
        })
        .catch(() => {});
    };
    const run = () => {
      if (timer) clearInterval(timer);
      tick();
      timer = setInterval(tick, EVERY);
    };
    run();
    const sub = AppState.addEventListener('change', (s) => {
      if (s === 'active') run();
      else if (timer) { clearInterval(timer); timer = null; }
    });
    return () => {
      if (timer) clearInterval(timer);
      sub.remove();
    };
  }, [customer]);

  return null;
}
