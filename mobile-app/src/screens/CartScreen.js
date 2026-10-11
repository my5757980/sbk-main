/* Your Cart - the website's cart, with the subtotal and the way to checkout. */
import { useEffect, useState } from 'react';
import { ScrollView, StyleSheet, View } from 'react-native';

import { C, R, lift, useUI } from '../theme';
import * as api from '../api';
import { CHECKOUT_TEXT } from '../api/static';
import { Button, Divider, T } from '../components/base';
import { Empty, TabHeader, Wrap } from '../components/layout';
import ListItem from '../components/ListItem';
import { useShop } from '../store/shop';
import { useGo } from '../nav';
import { money } from '../format';

export default function CartScreen() {
  const ui = useUI();
  const go = useGo();
  const shop = useShop();
  const [items, setItems] = useState(null);

  useEffect(() => {
    if (shop.ready) api.getProductsBySlugs(shop.cart).then(setItems);
  }, [shop.ready, shop.cart]);

  const subtotal = (items || []).reduce((sum, p) => sum + p.price, 0);

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader title="Your Cart" sub={items && items.length ? items.length + (items.length === 1 ? ' vehicle' : ' vehicles') : ' '} />
      {items && !items.length ? (
        <Empty
          icon="cart-outline"
          title="Your cart is empty."
          text="Add a vehicle from the shop and checkout when you are ready. SBK confirms availability and shipping before any payment."
          action="Browse vehicles"
          onAction={() => go.shop()}
          second={shop.wish.length ? 'Open Wishlist (' + shop.wish.length + ')' : undefined}
          onSecond={() => go.go('Wishlist')}
        />
      ) : (
        <>
          <ScrollView contentContainerStyle={{ paddingTop: 14, paddingBottom: 24 }}>
            <Wrap>
              {(items || []).map((p) => (
                <ListItem key={p.slug} product={p} onRemove={() => shop.removeFromCart(p.slug)} />
              ))}
              {items ? (
                <View style={styles.summary}>
                  <T v="h3">Order Summary</T>
                  <View style={styles.line}>
                    <T v="body" color={C.muted}>Vehicles</T>
                    <T v="title">{items.length}</T>
                  </View>
                  <Divider />
                  <View style={styles.line}>
                    <T v="title">Subtotal (FOB)</T>
                    <T v="h3" weight="900">{money(subtotal)}</T>
                  </View>
                  <T v="small" color={C.muted} weight="500">{CHECKOUT_TEXT.summaryNote}</T>
                </View>
              ) : null}
            </Wrap>
          </ScrollView>
          {items && items.length ? (
            <View style={styles.bar}>
              <Wrap style={{ flexDirection: 'row', alignItems: 'center' }}>
                <View style={{ flex: 1 }}>
                  <T v="label" size={10} color={C.muted}>Subtotal</T>
                  <T v="h3" weight="900" lines={1} adjustsFontSizeToFit>{money(subtotal)}</T>
                </View>
                <Button title="Proceed To Checkout" iconRight="arrow-forward" onPress={() => go.go('Checkout')} style={{ marginLeft: 12, minWidth: ui.width < 360 ? 170 : 200 }} />
              </Wrap>
            </View>
          ) : null}
        </>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  summary: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, marginTop: 4, ...lift },
  line: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 10 },
  bar: { backgroundColor: C.card, borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: C.line, paddingVertical: 10 },
});
