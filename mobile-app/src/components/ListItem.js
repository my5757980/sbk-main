/* One vehicle in the cart or the wishlist: picture, title, brand / model,
   price, and its actions on the right. */
import { StyleSheet, View } from 'react-native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, R, lift } from '../theme';
import { money } from '../format';
import { useGo } from '../nav';
import { Img, Press, T } from './base';

export default function ListItem({ product: p, children, onRemove }) {
  const go = useGo();
  return (
    <View style={styles.row}>
      <Press onPress={() => go.product(p.slug)} style={styles.main} accessibilityLabel={p.title}>
        <Img src={p.image} style={styles.photo} />
        <View style={{ flex: 1, marginLeft: 12 }}>
          <T v="title" size={14} lines={2}>{p.title}</T>
          <T v="small" color={C.muted} weight="500" lines={1} style={{ marginTop: 3 }}>{p.brand}{p.model ? ' / ' + p.model : ''}</T>
          <T v="h3" size={16} color={C.red} weight="900" style={{ marginTop: 6 }}>{money(p.price)}</T>
        </View>
      </Press>
      <View style={styles.side}>
        {onRemove ? (
          <Press onPress={onRemove} style={styles.remove} accessibilityLabel={'Remove ' + p.title} borderless>
            <Ionicons name="trash-outline" size={19} color={C.muted} />
          </Press>
        ) : null}
        {children}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', backgroundColor: C.card, borderRadius: R.lg, padding: 10, marginBottom: 12, ...lift },
  main: { flex: 1, flexDirection: 'row', alignItems: 'center' },
  photo: { width: 104, height: 78, borderRadius: R.md },
  side: { justifyContent: 'space-between', alignItems: 'flex-end', marginLeft: 6 },
  remove: { width: 40, height: 40, alignItems: 'center', justifyContent: 'center' },
});
