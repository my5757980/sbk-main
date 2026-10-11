/* A sheet that slides up from the bottom: the sort order, a list to pick from.
   Android's Back closes it before anything else, as a sheet should. */
import { useEffect, useRef } from 'react';
import { Animated, Easing, Modal, Pressable, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { C, R, useUI } from '../theme';
import { IconButton, T } from './base';

export default function Sheet({ open, onClose, title, children, tall }) {
  const ui = useUI();
  const insets = useSafeAreaInsets();
  const y = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    if (open) {
      y.setValue(1);
      Animated.timing(y, { toValue: 0, duration: 260, easing: Easing.out(Easing.cubic), useNativeDriver: true }).start();
    }
  }, [open, y]);

  const maxH = Math.round(ui.height * (tall ? 0.88 : 0.72));

  return (
    <Modal visible={!!open} transparent animationType="fade" onRequestClose={onClose} statusBarTranslucent navigationBarTranslucent>
      <View style={styles.root}>
        <Pressable style={styles.backdrop} onPress={onClose} accessibilityLabel="Close" />
        <Animated.View
          style={[
            styles.panel,
            {
              maxHeight: maxH,
              paddingBottom: Math.max(insets.bottom, 12),
              width: ui.tablet ? Math.min(560, ui.width - 48) : '100%',
              transform: [{ translateY: y.interpolate({ inputRange: [0, 1], outputRange: [0, maxH] }) }],
            },
            tall && { height: maxH },
          ]}
        >
          <View style={styles.handle} />
          {title ? (
            <View style={styles.head}>
              <T v="h3" style={{ flex: 1 }}>{title}</T>
              <IconButton name="close" kind="wash" size={36} onPress={onClose} label="Close" />
            </View>
          ) : null}
          {children}
        </Animated.View>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, justifyContent: 'flex-end', alignItems: 'center' },
  backdrop: { ...StyleSheet.absoluteFillObject, backgroundColor: 'rgba(11,12,15,0.5)' },
  panel: {
    backgroundColor: C.card,
    borderTopLeftRadius: R.xl,
    borderTopRightRadius: R.xl,
    overflow: 'hidden',
  },
  handle: { alignSelf: 'center', width: 40, height: 4, borderRadius: 2, backgroundColor: C.line, marginTop: 10, marginBottom: 4 },
  head: { flexDirection: 'row', alignItems: 'center', paddingHorizontal: 18, paddingTop: 6, paddingBottom: 10 },
});
