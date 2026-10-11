/* A description written in the website's admin editor, drawn natively:
   headings, paragraphs, bullet lists and two-column feature tables. */
import { StyleSheet, View } from 'react-native';

import { C, R } from '../theme';
import { T } from './base';

export default function Blocks({ blocks }) {
  return (
    <View>
      {(blocks || []).map((b, i) => {
        if (b.type === 'heading') {
          return <T key={i} v="h3" style={{ marginTop: i ? 16 : 0, marginBottom: 6 }}>{b.text}</T>;
        }
        if (b.type === 'list') {
          return (
            <View key={i} style={{ marginBottom: 10 }}>
              {b.items.map((it, j) => (
                <View key={j} style={styles.li}>
                  <View style={styles.bullet} />
                  <T v="body" color={C.ink2} style={{ flex: 1 }}>{it}</T>
                </View>
              ))}
            </View>
          );
        }
        if (b.type === 'table') {
          return (
            <View key={i} style={styles.table}>
              {b.rows.map(([k, v], j) => (
                <View key={j} style={[styles.row, j === b.rows.length - 1 && { borderBottomWidth: 0 }]}>
                  <T v="small" weight="800" color={C.ink} style={styles.key}>{k}</T>
                  <T v="sub" color={C.ink2} style={{ flex: 1 }}>{v}</T>
                </View>
              ))}
            </View>
          );
        }
        return <T key={i} v="body" color={C.ink2} style={{ marginBottom: 10 }}>{b.text}</T>;
      })}
    </View>
  );
}

const styles = StyleSheet.create({
  li: { flexDirection: 'row', alignItems: 'flex-start', marginBottom: 6 },
  bullet: { width: 6, height: 6, borderRadius: 3, backgroundColor: C.red, marginTop: 8, marginRight: 10 },
  table: { borderWidth: 1, borderColor: C.line, borderRadius: R.md, overflow: 'hidden', marginVertical: 8 },
  row: { flexDirection: 'row', paddingVertical: 10, paddingHorizontal: 12, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line, backgroundColor: C.card },
  key: { width: '38%', paddingRight: 10 },
});
