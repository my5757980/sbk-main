/* The live chat's state on the phone - the website's own chat.

   A signed-in customer is known by their account token. A visitor gives name,
   e-mail and telephone once and keeps the chat's visitor token in the secure
   store (the website keeps the same value in a browser cookie), so tomorrow
   they are the same person with the same conversation. The unread count and a
   ringing call are picked up by ChatBeat (src/calls/beat.js). */
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import * as SecureStore from 'expo-secure-store';
import AsyncStorage from '@react-native-async-storage/async-storage';

import * as chatApi from '../api/chat';
import { setGuestToken } from '../api/http';
import { useAccount } from './account';

const GUEST = 'sbk.chat.guest.v1';

const ChatContext = createContext(null);

export function ChatProvider({ children }) {
  const account = useAccount();
  const [guest, setGuest] = useState(null);
  const [loaded, setLoaded] = useState(false);
  const [who, setWho] = useState(null); // { me, staff, details }
  const [checking, setChecking] = useState(true);
  const [unread, setUnread] = useState(0);
  const [draft, setDraft] = useState('');
  const userId = account.user ? account.user.id : null;

  useEffect(() => {
    // The phase-1 preview kept pretend messages on the phone; they were never sent.
    AsyncStorage.multiRemove(['sbk.chat.me.v1', 'sbk.chat.preview.v1']).catch(() => {});
    SecureStore.getItemAsync(GUEST)
      .then((t) => {
        if (t) {
          setGuestToken(t);
          setGuest(t);
        }
      })
      .catch(() => {})
      .finally(() => setLoaded(true));
  }, []);

  const forgetGuest = useCallback(() => {
    SecureStore.deleteItemAsync(GUEST).catch(() => {});
    setGuestToken(null);
    setGuest(null);
  }, []);

  /** Ask the website who this phone is in the chat. */
  const refreshWho = useCallback(async () => {
    if (!userId && !guest) {
      setWho(null);
      setChecking(false);
      return null;
    }
    setChecking(true);
    try {
      const r = await chatApi.who();
      const w = { me: r.me || {}, staff: !!r.staff, details: !!r.details };
      setWho(w);
      return w;
    } catch (e) {
      if (e.status === 401) {
        if (!userId) forgetGuest();
        setWho(null);
      }
      return null;
    } finally {
      setChecking(false);
    }
  }, [userId, guest, forgetGuest]);

  useEffect(() => {
    if (loaded && account.ready) refreshWho();
  }, [loaded, account.ready, refreshWho]);

  const value = useMemo(() => ({
    ready: loaded && account.ready && !checking,
    who,
    unread,
    setUnread,
    draft,
    setDraft,
    refreshWho,
    /** The way in: name, e-mail and telephone (a signed-in customer's e-mail is fixed). */
    async start(details) {
      const r = await chatApi.start(details);
      if (r.guestToken) {
        await SecureStore.setItemAsync(GUEST, r.guestToken).catch(() => {});
        setGuestToken(r.guestToken);
        setGuest(r.guestToken);
      }
      const w = { me: r.me || {}, staff: !!r.staff, details: !!r.details };
      setWho(w);
      return w;
    },
    /** A visitor leaves the chat on this phone (the conversation stays on the website). */
    leave() {
      forgetGuest();
      setWho(null);
      setUnread(0);
    },
  }), [loaded, account.ready, checking, who, unread, draft, refreshWho, forgetGuest]);

  return <ChatContext.Provider value={value}>{children}</ChatContext.Provider>;
}

export function useChat() {
  return useContext(ChatContext);
}
