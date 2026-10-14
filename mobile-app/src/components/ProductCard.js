/* A vehicle, as a card: the website's sbk-car card (picture, tag, wishlist
   heart, red brand, title, year / mileage / fuel, FOB price, cart button). */
import { memo } from 'react';
import { StyleSheet, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, R, lift, useUI } from '../theme';
import { money, mileageLabel } from '../format';
import { useShop } from '../store/shop';
import { toTab } from '../nav';
import { Img, Pill, Press, T } from './base';

/* "5% OFF, Japanese Used Cars" -> "5% OFF": a card has room for one tag. */
export function tagOf(p) {
  const first = String(p.tag || '').split(',')[0].trim();
  return first.length > 22 ? first.slice(0, 21) + '…' : first;
}

export function tagTone(tag) {
  return /reserved/i.test(tag) ? 'dark' : 'red';
}

function ProductCard({ product: p, width, style }) {
  const ui = useUI();
  const nav = useNavigation();
  const shop = useShop();
  const tag = tagOf(p);
  const inCart = shop.inCart(p.slug);
  const inWish = shop.inWish(p.slug);
  const narrow = (width || ui.width) < 190;

  // A card given a width keeps it. With flexGrow as well, React Native's
  // layout (Yoga's legacy stretch) widened the card while measuring a
  // horizontal list, the 4:3 photo grew with it, and every card in the rail
  // came out far too tall (Mohsin's phone, 14 Oct: price and cart button out
  // of sight). The browser preview lays out differently and never showed it.
  return (
    <View style={[styles.card, width ? { width } : { flexGrow: 1 }, style]}>
      <Press onPress={() => nav.navigate('Product', { slug: p.slug })} style={{ flex: 1 }} accessibilityLabel={p.title}>
        <View>
          <Img src={p.image} style={styles.photo} />
          {tag ? <Pill text={tag} tone={tagTone(tag)} style={styles.tag} /> : null}
        </View>
        <View style={[styles.body, narrow && { padding: 10 }, { flex: 1 }]}>
          <T v="eyebrow" size={10.5} color={C.red} lines={1} style={{ marginBottom: 4 }}>{p.brand}</T>
          <T v="title" size={narrow ? 13.5 : 14.5} lines={2} style={{ minHeight: ui.t(narrow ? 13.5 : 14.5) * 2.7 }}>{p.title}</T>
          <View style={styles.specs}>
            <Spec icon="calendar-outline" text={p.year ? String(p.year) : 'N/A'} />
            <Spec icon="speedometer-outline" text={mileageLabel(p)} />
            {!narrow ? <Spec icon="water-outline" text={p.fuel || 'N/A'} /> : null}
          </View>
          <View style={styles.foot}>
            <View style={{ flex: 1 }}>
              <T v="label" size={10} color={C.muted} style={{ letterSpacing: 0.4 }}>FOB Price</T>
              <T v="h3" size={narrow ? 15 : 16.5} weight="900" lines={1} adjustsFontSizeToFit>{money(p.price)}</T>
            </View>
            <Press
              onPress={() => (inCart ? toTab(nav, 'Cart') : shop.addToCart(p.slug))}
              style={[styles.cartBtn, inCart && { backgroundColor: C.ink }]}
              ripple="rgba(255,255,255,0.25)"
              accessibilityLabel={inCart ? 'In cart' : 'Add to cart'}
            >
              <Ionicons name={inCart ? 'checkmark' : 'cart-outline'} size={19} color={C.white} />
            </Press>
          </View>
        </View>
      </Press>
      <Press
        onPress={() => shop.toggleWish(p.slug)}
        style={styles.heart}
        hitSlop={6}
        accessibilityLabel={inWish ? 'Remove from wishlist' : 'Add to wishlist'}
        borderless
      >
        <Ionicons name={inWish ? 'heart' : 'heart-outline'} size={19} color={inWish ? C.red : C.ink} />
      </Press>
    </View>
  );
}

function Spec({ icon, text }) {
  return (
    <View style={styles.spec}>
      <Ionicons name={icon} size={12} color={C.faint} style={{ marginRight: 3 }} />
      <T v="small" size={11.5} weight="500" color={C.muted} lines={1}>{text}</T>
    </View>
  );
}

export default memo(ProductCard);

const styles = StyleSheet.create({
  card: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  photo: { width: '100%', aspectRatio: 4 / 3 },
  tag: { position: 'absolute', left: 10, top: 10 },
  heart: {
    position: 'absolute',
    right: 10,
    top: 10,
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.94)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  body: { padding: 12 },
  specs: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 8, marginBottom: 10, columnGap: 10, rowGap: 4 },
  spec: { flexDirection: 'row', alignItems: 'center', maxWidth: '100%' },
  foot: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 'auto',
    paddingTop: 10,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: C.line,
  },
  cartBtn: { width: 42, height: 42, borderRadius: R.md, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center', overflow: 'hidden', marginLeft: 8 },
});
