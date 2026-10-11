/* The customer's account.

   PHASE 1 (now): nothing is sent to the website yet, so signing in or
   registering keeps the details on this phone only, to show every account
   screen as it will be. The screens say so. PHASE 2: signIn/register call the
   website, which returns the customer and a token; the token is kept here in
   the phone's secure store and sent with every request, and nothing else in
   the app changes. */
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import * as SecureStore from 'expo-secure-store';

const KEY = 'sbk.account.v1';
const PROFILES = 'sbk.profiles.v1';

const AccountContext = createContext(null);

async function read(key, fallback) {
  try {
    const raw = await SecureStore.getItemAsync(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch (e) {
    return fallback;
  }
}

function write(key, value) {
  const job = value == null ? SecureStore.deleteItemAsync(key) : SecureStore.setItemAsync(key, JSON.stringify(value));
  job.catch(() => {});
}

const clean = (u) => ({
  name: String(u.name || '').trim(),
  email: String(u.email || '').trim().toLowerCase(),
  phone: String(u.phone || '').trim(),
  country: String(u.country || '').trim(),
  photo: u.photo || null,
});

export function AccountProvider({ children }) {
  const [user, setUser] = useState(null);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    read(KEY, null).then((u) => {
      setUser(u && u.email ? u : null);
      setReady(true);
    });
  }, []);

  const keep = useCallback((u) => {
    setUser(u);
    write(KEY, u);
    if (u) {
      // Remember who registered on this phone, so signing in again brings
      // their details back (phase 1 only; the website does this in phase 2).
      read(PROFILES, {}).then((all) => write(PROFILES, { ...all, [u.email]: u }));
    }
  }, []);

  const value = useMemo(() => ({
    ready,
    user,
    preview: true,
    async signIn({ email }) {
      const all = await read(PROFILES, {});
      const known = all[String(email).trim().toLowerCase()];
      keep(known || clean({ email }));
    },
    async register(fields) {
      keep(clean(fields));
    },
    async update(fields) {
      keep(clean({ ...user, ...fields }));
    },
    async changePassword() {
      return true;
    },
    signOut() {
      keep(null);
    },
  }), [ready, user, keep]);

  return <AccountContext.Provider value={value}>{children}</AccountContext.Provider>;
}

export function useAccount() {
  return useContext(AccountContext);
}
