/* The customer's account - ONE account with the website.

   Signing in checks the website's own users table and returns a token, kept in
   the phone's secure store and sent with every call; signing out ends that
   token on the website. The account is checked with the website each time the
   app opens, so a password changed or an account removed on the website takes
   effect here too. */
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import * as SecureStore from 'expo-secure-store';

import * as api from '../api';
import { setAuthToken } from '../api/http';

const KEY = 'sbk.account.v2';

const AccountContext = createContext(null);

async function readSaved() {
  try {
    const raw = await SecureStore.getItemAsync(KEY);
    const v = raw ? JSON.parse(raw) : null;
    return v && v.token && v.user ? v : null;
  } catch (e) {
    return null;
  }
}

function save(v) {
  const job = v ? SecureStore.setItemAsync(KEY, JSON.stringify(v)) : SecureStore.deleteItemAsync(KEY);
  job.catch(() => {});
}

export function AccountProvider({ children }) {
  const [session, setSession] = useState(null); // { token, user }
  const [ready, setReady] = useState(false);

  const adopt = useCallback((v) => {
    setAuthToken(v ? v.token : null);
    setSession(v);
    save(v);
  }, []);

  useEffect(() => {
    // The phase-1 preview kept a pretend account here; it never existed on the website.
    SecureStore.deleteItemAsync('sbk.account.v1').catch(() => {});
    readSaved().then((v) => {
      if (v) setAuthToken(v.token);
      setSession(v);
      setReady(true);
      if (!v) return;
      api.me()
        .then((user) => adopt({ token: v.token, user }))
        .catch((e) => {
          if (e instanceof api.ApiError && e.status === 401) adopt(null);
        });
    });
  }, [adopt]);

  const value = useMemo(() => ({
    ready,
    user: session ? session.user : null,
    async signIn({ email, password }) {
      const r = await api.login(String(email).trim(), password);
      adopt({ token: r.token, user: r.user });
      return r.user;
    },
    async register(fields) {
      const r = await api.register(fields);
      adopt({ token: r.token, user: r.user });
      return r.user;
    },
    /** A token the website handed over elsewhere (a new customer's checkout). */
    adopt(token, user) {
      adopt({ token, user });
    },
    async update(fields, photo) {
      const r = await api.updateProfile(fields, photo);
      if (session) adopt({ token: session.token, user: r.user });
      return r.message;
    },
    async changePassword(current, next, confirm) {
      const r = await api.changePassword(current, next, confirm);
      return r.message;
    },
    async forgot(email) {
      const r = await api.forgotPassword(String(email).trim());
      return r.message;
    },
    async orders() {
      return api.getOrders();
    },
    signOut() {
      api.logout().catch(() => {});
      adopt(null);
    },
  }), [ready, session, adopt]);

  return <AccountContext.Provider value={value}>{children}</AccountContext.Provider>;
}

export function useAccount() {
  return useContext(AccountContext);
}
