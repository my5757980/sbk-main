/* Your Wishlist - saved vehicles, each one tap from the cart. */
import { useEffect, useState } from 'react';
import { ScrollView, View } from 'react-native';

import { C } from '../theme';
import * as api from '../api';
import { Button } from '../components/base';
import { Empty, Wrap } from '../components/layout';
import ListItem from '../components/ListItem';
import { useShop } from '../store/shop';
import { useGo } from '../nav';

export default function WishlistScreen() {
  const go = useGo();
  const shop = useShop();
  const [items, setItems] = useState(null);

  useEffect(() => {
    if (shop.ready) api.getProductsBySlugs(shop.wish).then(setItems);
  }, [shop.ready, shop.wish]);

  if (items && !items.length) {
    return (
      <View style={{ flex: 1, backgroundColor: C.bg }}>
        <Empty
          icon="heart-outline"
          title="Your wishlist is empty."
          text="Tap the heart on any vehicle to keep it here."
          action="Browse vehicles"
          onAction={() => go.shop()}
        />
      </View>
    );
  }

  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingTop: 14, paddingBottom: 28 }}>
      <Wrap>
        {(items || []).map((p) => {
          const inCart = shop.inCart(p.slug);
          return (
            <ListItem key={p.slug} product={p} onRemove={() => shop.removeFromWish(p.slug)}>
              <Button
                title={inCart ? 'In Cart' : 'Add Cart'}
                size="sm"
                kind={inCart ? 'outline' : 'dark'}
                icon={inCart ? 'checkmark' : undefined}
                onPress={() => (inCart ? go.tab('Cart') : shop.addToCart(p.slug))}
              />
            </ListItem>
          );
        })}
      </Wrap>
    </ScrollView>
  );
}
