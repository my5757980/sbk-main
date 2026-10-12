/* The cart and the wishlist, kept on the phone.

   On the website both belong to the visitor's session; here they belong to the
   phone and survive the app being closed. Each vehicle is one of a kind, so a
   cart line is a vehicle, not a quantity. In phase 2 they are sent with the
   order to the website. */
import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';

import * as api from '../api';

const CART = 'sbk.cart.v1';
const WISH = 'sbk.wishlist.v1';

const ShopContext = createContext(null);

async function load(key) {
  try {
    const raw = await AsyncStorage.getItem(key);
    const list = raw ? JSON.parse(raw) : [];
    return Array.isArray(list) ? list.filter((s) => typeof s === 'string') : [];
  } catch (e) {
    return [];
  }
}

function save(key, list) {
  AsyncStorage.setItem(key, JSON.stringify(list)).catch(() => {});
}

export function ShopProvider({ children }) {
  const [cart, setCart] = useState([]);
  const [wish, setWish] = useState([]);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    let live = true;
    Promise.all([load(CART), load(WISH)]).then(async ([c, w]) => {
      let cart = c;
      let wish = w;
      try {
        // A vehicle sold or taken off the website since it was saved drops out.
        const [cs, ws] = await Promise.all([api.getProductsBySlugs(c), api.getProductsBySlugs(w)]);
        cart = cs.map((p) => p.slug);
        wish = ws.map((p) => p.slug);
      } catch (e) {
        // Without the website the saved lists stay as they are.
      }
      if (!live) return;
      setCart(cart);
      setWish(wish);
      setReady(true);
    });
    return () => { live = false; };
  }, []);

  const change = useCallback((key, setter, fn) => {
    setter((prev) => {
      const next = fn(prev);
      save(key, next);
      return next;
    });
  }, []);

  const value = useMemo(() => ({
    ready,
    cart,
    wish,
    inCart: (slug) => cart.includes(slug),
    inWish: (slug) => wish.includes(slug),
    addToCart: (slug) => change(CART, setCart, (l) => (l.includes(slug) ? l : [...l, slug])),
    removeFromCart: (slug) => change(CART, setCart, (l) => l.filter((s) => s !== slug)),
    clearCart: () => change(CART, setCart, () => []),
    toggleWish: (slug) => change(WISH, setWish, (l) => (l.includes(slug) ? l.filter((s) => s !== slug) : [slug, ...l])),
    removeFromWish: (slug) => change(WISH, setWish, (l) => l.filter((s) => s !== slug)),
  }), [ready, cart, wish, change]);

  return <ShopContext.Provider value={value}>{children}</ShopContext.Provider>;
}

export function useShop() {
  return useContext(ShopContext);
}
