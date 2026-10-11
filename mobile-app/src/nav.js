/* Going places. One set of helpers so every screen opens the shop, a vehicle,
   the chat or a page the same way. */
import { useMemo } from 'react';
import { useNavigation } from '@react-navigation/native';

import { useFilters } from './store/filters';

export function useGo() {
  const nav = useNavigation();
  const { setFilters } = useFilters();
  return useMemo(() => ({
    nav,
    /** The shop, optionally with filters set (the rest cleared). */
    shop: (f) => {
      if (f) setFilters(f);
      nav.navigate('Tabs', { screen: 'Shop' });
    },
    tab: (name, params) => nav.navigate('Tabs', { screen: name, params }),
    /** A vehicle. Pushed, so a related vehicle opens on top and Back returns. */
    product: (slug) => (nav.push ? nav.push('Product', { slug }) : nav.navigate('Product', { slug })),
    /** The live chat, optionally with a first message ready to send. */
    chat: (draft) => nav.navigate('Tabs', { screen: 'Chat', params: draft ? { draft, at: Date.now() } : undefined }),
    contact: (subject) => nav.navigate('Contact', subject ? { subject, at: Date.now() } : undefined),
    go: (name, params) => nav.navigate(name, params),
  }), [nav, setFilters]);
}
