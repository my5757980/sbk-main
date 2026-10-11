/* The live chat's state on the phone.

   The chat is the website's own live chat (routes/chat.php on the website's
   database): a customer gives name, e-mail and telephone once, sees the team,
   and talks to them - text, pictures, voice notes, calls.

   PHASE 1 (now): the screens are built and work on the phone, but nothing
   reaches the website yet; a message stays on this phone and says it is not
   sent. PHASE 2: `send` posts to the website's chat API and `poll` brings the
   team's replies, the same endpoints the website's chat page uses. */
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';

const ME = 'sbk.chat.me.v1';
const LOG = 'sbk.chat.preview.v1';

const ChatContext = createContext(null);

export function ChatProvider({ children }) {
  const [me, setMeState] = useState(null);
  const [messages, setMessages] = useState([]);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    Promise.all([AsyncStorage.getItem(ME), AsyncStorage.getItem(LOG)])
      .then(([m, l]) => {
        try { setMeState(m ? JSON.parse(m) : null); } catch (e) { /* start clean */ }
        try { setMessages(l ? JSON.parse(l) : []); } catch (e) { /* start clean */ }
      })
      .catch(() => {})
      .finally(() => setReady(true));
  }, []);

  const setMe = useCallback((details) => {
    const v = details ? {
      name: String(details.name || '').trim(),
      email: String(details.email || '').trim().toLowerCase(),
      phone: String(details.phone || '').trim(),
    } : null;
    setMeState(v);
    (v ? AsyncStorage.setItem(ME, JSON.stringify(v)) : AsyncStorage.removeItem(ME)).catch(() => {});
  }, []);

  const push = useCallback((m) => {
    setMessages((prev) => {
      const next = [...prev, m].slice(-300);
      AsyncStorage.setItem(LOG, JSON.stringify(next)).catch(() => {});
      return next;
    });
  }, []);

  const value = useMemo(() => ({
    ready,
    me,
    setMe,
    messages,
    connected: false,
    send(text) {
      const body = String(text || '').trim();
      if (!body) return;
      push({ id: Date.now() + '-' + Math.random().toString(36).slice(2, 7), mine: true, kind: 'text', text: body, at: new Date().toISOString(), status: 'queued' });
    },
    sendPicture(uri, width, height) {
      if (!uri) return;
      push({ id: Date.now() + '-p', mine: true, kind: 'image', uri, width, height, at: new Date().toISOString(), status: 'queued' });
    },
    clear() {
      setMessages([]);
      AsyncStorage.removeItem(LOG).catch(() => {});
    },
  }), [ready, me, setMe, messages, push]);

  return <ChatContext.Provider value={value}>{children}</ChatContext.Provider>;
}

export function useChat() {
  return useContext(ChatContext);
}
