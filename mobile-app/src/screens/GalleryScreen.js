/* A vehicle's photos, full screen on black, swiped one by one, with the
   thumbnails underneath - the website's gallery, as a phone does it. */
import { useEffect, useRef, useState } from 'react';
import { FlatList, StyleSheet, View } from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';

import { C, R, useUI } from '../theme';
import { IconButton, Img, Press, T } from '../components/base';

export default function GalleryScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const { images = [], index: start = 0, title } = useRoute().params || {};
  const insets = useSafeAreaInsets();
  const [index, setIndex] = useState(start);
  const pager = useRef(null);
  const strip = useRef(null);

  useEffect(() => {
    strip.current?.scrollToIndex({ index, animated: true, viewPosition: 0.5 });
  }, [index]);

  const jump = (i) => {
    setIndex(i);
    pager.current?.scrollToOffset({ offset: i * ui.width, animated: true });
  };

  return (
    <View style={styles.root}>
      <StatusBar style="light" />
      <View style={[styles.top, { paddingTop: insets.top + 4, paddingHorizontal: ui.gutter - 4 }]}>
        <IconButton name="close" kind="dark" onPress={() => nav.goBack()} label="Close" />
        <View style={{ flex: 1, paddingHorizontal: 12 }}>
          <T v="title" color={C.white} lines={1}>{title}</T>
          <T v="small" color="rgba(255,255,255,0.6)" weight="600">{index + 1} of {images.length}</T>
        </View>
      </View>
      <FlatList
        ref={pager}
        data={images}
        horizontal
        pagingEnabled
        initialScrollIndex={start}
        getItemLayout={(_, i) => ({ length: ui.width, offset: ui.width * i, index: i })}
        showsHorizontalScrollIndicator={false}
        keyExtractor={(src, i) => src + i}
        onMomentumScrollEnd={(e) => setIndex(Math.round(e.nativeEvent.contentOffset.x / ui.width))}
        renderItem={({ item }) => (
          <View style={{ width: ui.width, flex: 1, justifyContent: 'center' }}>
            <Img src={item} fit="contain" plain style={{ width: ui.width, height: '100%' }} />
          </View>
        )}
        style={{ flex: 1 }}
      />
      <FlatList
        ref={strip}
        data={images}
        horizontal
        showsHorizontalScrollIndicator={false}
        keyExtractor={(src, i) => 't' + src + i}
        getItemLayout={(_, i) => ({ length: 72, offset: 72 * i, index: i })}
        onScrollToIndexFailed={() => {}}
        contentContainerStyle={{ paddingHorizontal: ui.gutter, paddingTop: 10, paddingBottom: Math.max(insets.bottom, 12) + 4 }}
        style={{ flexGrow: 0 }}
        renderItem={({ item, index: i }) => (
          <Press onPress={() => jump(i)} style={[styles.thumb, i === index && styles.thumbOn]} accessibilityLabel={'Photo ' + (i + 1)}>
            <Img src={item} style={{ width: 60, height: 45, borderRadius: R.xs }} />
          </Press>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#000' },
  top: { flexDirection: 'row', alignItems: 'center', paddingBottom: 8 },
  thumb: { width: 64, height: 49, marginRight: 8, borderRadius: R.sm, borderWidth: 2, borderColor: 'transparent', alignItems: 'center', justifyContent: 'center', opacity: 0.6 },
  thumbOn: { borderColor: C.red, opacity: 1 },
});
