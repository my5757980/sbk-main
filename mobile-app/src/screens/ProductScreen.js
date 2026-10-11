/* Vehicle detail - the website's product page: the photos, the facts, the
   price, every specification, the description, and related vehicles, with
   the buying actions always at the bottom of the screen. */
import { useEffect, useMemo, useRef, useState } from 'react';
import { Animated, FlatList, StyleSheet, View } from 'react-native';
import { useIsFocused, useNavigation, useRoute } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, R, lift, useUI } from '../theme';
import * as api from '../api';
import { PRODUCT_TEXT } from '../api/static';
import { Button, IconButton, Img, Pill, Press, T } from '../components/base';
import { Empty, Notice, SectionHead, Wrap } from '../components/layout';
import Blocks from '../components/Blocks';
import ProductCard, { tagOf, tagTone } from '../components/ProductCard';
import { useShop } from '../store/shop';
import { useGo } from '../nav';
import { money, mileageLabel } from '../format';
import { shareProduct } from '../actions';

const FIRST_SPECS = 10;

export default function ProductScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const go = useGo();
  const route = useRoute();
  const insets = useSafeAreaInsets();
  const shop = useShop();
  const [p, setP] = useState(undefined);
  const [related, setRelated] = useState([]);
  const [photo, setPhoto] = useState(0);
  const [allSpecs, setAllSpecs] = useState(false);
  const [longText, setLongText] = useState(false);
  const scrollY = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    let live = true;
    api.getProduct(route.params?.slug).then(async (found) => {
      if (!live) return;
      setP(found);
      if (found) setRelated(await api.getRelated(found));
    });
    return () => { live = false; };
  }, [route.params?.slug]);

  const photoH = Math.round(Math.min(ui.width * 0.75, ui.tablet ? 520 : 420));
  const barOpacity = scrollY.interpolate({ inputRange: [photoH - 120, photoH - 40], outputRange: [0, 1], extrapolate: 'clamp' });

  // Light status-bar icons over the photos, dark ones once the white bar is in.
  const focused = useIsFocused();
  const [solid, setSolid] = useState(false);
  useEffect(() => {
    const id = scrollY.addListener(({ value }) => setSolid((s) => (s !== value > photoH - 80 ? value > photoH - 80 : s)));
    return () => scrollY.removeListener(id);
  }, [scrollY, photoH]);

  const specs = useMemo(() => {
    if (!p) return [];
    // Spec labels as the website writes them; the second copy of a fact the
    // admin entered twice ("Color" and "Colour", "Chassis No" and "Chassis") is
    // shown once.
    const seen = new Set();
    return p.specs.filter(([label, value]) => {
      const key = label.toLowerCase().replace(/colour/, 'color').replace(/^chassis$/, 'chassis no') + '|' + String(value).toLowerCase();
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });
  }, [p]);

  if (p === undefined) return <View style={{ flex: 1, backgroundColor: C.bg }} />;
  if (p === null) {
    return (
      <View style={{ flex: 1, backgroundColor: C.bg, paddingTop: insets.top }}>
        <Empty icon="car-outline" title="This vehicle is no longer listed" text="It may have been sold. Browse the stock for similar vehicles." action="Browse Vehicles" onAction={() => go.shop({})} />
      </View>
    );
  }

  const tag = tagOf(p);
  const inCart = shop.inCart(p.slug);
  const inWish = shop.inWish(p.slug);
  const images = p.images.length ? p.images : [p.image];
  const facts = [
    ['calendar-outline', p.year ? String(p.year) : 'Year N/A'],
    ['speedometer-outline', mileageLabel(p)],
    ['water-outline', p.fuel || 'Fuel N/A'],
    ['location-outline', p.location || 'Japan Stock'],
  ];
  const summary = p.summary || PRODUCT_TEXT.summary;
  const shownSpecs = allSpecs ? specs : specs.slice(0, FIRST_SPECS);
  const half = ui.tablet ? '32%' : '48.5%';
  const bottomBar = 64 + Math.max(insets.bottom, 10);

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      {focused ? <StatusBar style={solid ? 'dark' : 'light'} /> : null}
      <Animated.ScrollView
        onScroll={Animated.event([{ nativeEvent: { contentOffset: { y: scrollY } } }], { useNativeDriver: true })}
        scrollEventThrottle={16}
        contentContainerStyle={{ paddingBottom: bottomBar + 24 }}
      >
        {/* photos */}
        <View style={{ height: photoH, backgroundColor: C.dark }}>
          <FlatList
            data={images}
            horizontal
            pagingEnabled
            showsHorizontalScrollIndicator={false}
            keyExtractor={(src, i) => src + i}
            onMomentumScrollEnd={(e) => setPhoto(Math.round(e.nativeEvent.contentOffset.x / ui.width))}
            renderItem={({ item, index }) => (
              <Press onPress={() => nav.navigate('Gallery', { images, index, title: p.title })} accessibilityLabel={'Photo ' + (index + 1)}>
                <Img src={item} style={{ width: ui.width, height: photoH }} priority={index === 0 ? 'high' : 'normal'} />
              </Press>
            )}
          />
          <LinearGradient colors={['rgba(11,12,15,0.55)', 'transparent']} style={[styles.topShade, { height: insets.top + 70 }]} pointerEvents="none" />
          <View style={styles.counter} pointerEvents="none">
            <Ionicons name="images-outline" size={13} color={C.white} />
            <T v="small" color={C.white} weight="800" style={{ marginLeft: 5 }}>{photo + 1} / {images.length}</T>
          </View>
        </View>

        {/* title block */}
        <View style={styles.sheet}>
          <Wrap style={{ paddingTop: 20 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 6 }}>
              <T v="eyebrow" color={C.red} style={{ flex: 1 }} lines={1}>{p.brand}{p.model ? ' / ' + p.model : ''}</T>
              {tag ? <Pill text={tag} tone={tagTone(tag)} /> : null}
            </View>
            <T v="h1" size={ui.width < 360 ? 22 : 24}>{p.title}</T>
            <View style={styles.facts}>
              {facts.map(([icon, text]) => (
                <View key={icon} style={styles.fact}>
                  <Ionicons name={icon} size={14} color={C.red} />
                  <T v="small" weight="700" color={C.ink2} style={{ marginLeft: 5 }}>{text}</T>
                </View>
              ))}
            </View>
            <T v="body" color={C.muted} lines={longText ? undefined : 4} style={{ marginTop: 14 }}>{summary}</T>
            {summary.length > 220 ? (
              <Press onPress={() => setLongText((v) => !v)} style={{ alignSelf: 'flex-start', minHeight: 36, justifyContent: 'center' }}>
                <T v="small" color={C.red} weight="800">{longText ? 'Show less' : 'Read more'}</T>
              </Press>
            ) : null}

            {/* price */}
            <LinearGradient colors={['#1A1C22', '#0B0C0F']} style={styles.priceCard}>
              <View style={{ flex: 1 }}>
                <T v="label" color="rgba(255,255,255,0.65)">FOB Price</T>
                <T v="display" size={28} color={C.white} style={{ marginTop: 4 }} adjustsFontSizeToFit lines={1}>{money(p.price)}</T>
                <T v="small" color="rgba(255,255,255,0.65)" weight="500" style={{ marginTop: 6 }}>{PRODUCT_TEXT.priceNote}</T>
              </View>
            </LinearGradient>
          </Wrap>
        </View>

        {/* specifications */}
        <Wrap style={{ paddingTop: 26 }}>
          <SectionHead title="Vehicle Specifications" />
          <View style={styles.specGrid}>
            {shownSpecs.map(([label, value], i) => (
              <View key={label + i} style={[styles.spec, { width: half }]}>
                <T v="label" size={10} color={C.muted} lines={2}>{label}</T>
                <T v="title" size={14} style={{ marginTop: 4 }}>{value}</T>
              </View>
            ))}
          </View>
          {specs.length > FIRST_SPECS ? (
            <Button
              title={allSpecs ? 'Show fewer' : 'Show all ' + specs.length + ' specifications'}
              kind="outline"
              size="sm"
              iconRight={allSpecs ? 'chevron-up' : 'chevron-down'}
              onPress={() => setAllSpecs((v) => !v)}
              style={{ alignSelf: 'center', marginTop: 4 }}
            />
          ) : null}
        </Wrap>

        {/* description */}
        <Wrap style={{ paddingTop: 28 }}>
          <SectionHead title="Description" />
          <View style={styles.descCard}>
            {p.description && p.description.length ? <Blocks blocks={p.description} /> : <T v="body" color={C.ink2}>{p.summary || PRODUCT_TEXT.description}</T>}
          </View>
          <Notice tone="info" text={PRODUCT_TEXT.buyNote} style={{ marginTop: 14 }} />
          <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 14 }}>
            <Button
              title="Ask About This Vehicle"
              icon="chatbubbles-outline"
              kind="outline"
              onPress={() => go.chat('Hi SBK, I am interested in ' + p.title + (p.refNo ? ' (Ref. ' + p.refNo + ')' : '') + '.')}
              style={{ flex: 1 }}
            />
          </View>
        </Wrap>

        {/* related */}
        {related.length ? (
          <View style={{ paddingTop: 30 }}>
            <Wrap>
              <SectionHead title="Related Vehicles" />
            </Wrap>
            <FlatList
              horizontal
              data={related}
              keyExtractor={(r) => r.slug}
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={{ paddingHorizontal: ui.edge, paddingBottom: 8 }}
              ItemSeparatorComponent={() => <View style={{ width: 12 }} />}
              renderItem={({ item }) => <ProductCard product={item} width={Math.round(Math.min(240, ui.width * 0.6))} />}
            />
          </View>
        ) : null}
      </Animated.ScrollView>

      {/* the bar that appears over the photos as they scroll away */}
      <Animated.View style={[styles.solidBar, { paddingTop: insets.top, opacity: barOpacity }]} pointerEvents="none">
        <View style={{ height: 52, justifyContent: 'center', paddingHorizontal: 64 }}>
          <T v="title" lines={1} center>{p.title}</T>
        </View>
      </Animated.View>
      <View style={[styles.floatBar, { top: insets.top + 6, paddingHorizontal: ui.gutter - 4 }]}>
        <IconButton name="arrow-back" kind="soft" onPress={() => nav.goBack()} label="Back" />
        <View style={{ flex: 1 }} />
        <IconButton name="share-social-outline" kind="soft" onPress={() => shareProduct(p)} label="Share" />
        <IconButton
          name={inWish ? 'heart' : 'heart-outline'}
          kind="soft"
          color={inWish ? C.red : C.ink}
          onPress={() => shop.toggleWish(p.slug)}
          label={inWish ? 'Remove from wishlist' : 'Add to wishlist'}
          style={{ marginLeft: 8 }}
        />
      </View>

      {/* buying actions */}
      <View style={[styles.buyBar, { paddingBottom: Math.max(insets.bottom, 10) }]}>
        <Wrap style={{ flexDirection: 'row', alignItems: 'center' }}>
          <View style={{ flex: 1, marginRight: 12 }}>
            <T v="label" size={10} color={C.muted}>FOB Price</T>
            <T v="h3" weight="900" lines={1} adjustsFontSizeToFit>{money(p.price)}</T>
          </View>
          {inCart ? (
            <Button title="Checkout" icon="lock-closed-outline" kind="dark" onPress={() => nav.navigate('Checkout')} style={{ minWidth: 150 }} />
          ) : (
            <Button title="Add to Cart" icon="cart-outline" onPress={() => shop.addToCart(p.slug)} style={{ minWidth: 150 }} />
          )}
        </Wrap>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  topShade: { position: 'absolute', top: 0, left: 0, right: 0 },
  counter: {
    position: 'absolute',
    right: 14,
    bottom: 30,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(11,12,15,0.62)',
    borderRadius: R.pill,
    paddingHorizontal: 10,
    paddingVertical: 5,
  },
  sheet: { backgroundColor: C.card, borderTopLeftRadius: 22, borderTopRightRadius: 22, marginTop: -18, paddingBottom: 22 },
  facts: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 14 },
  fact: { flexDirection: 'row', alignItems: 'center', backgroundColor: C.bg, borderRadius: R.pill, paddingHorizontal: 11, paddingVertical: 7 },
  priceCard: { flexDirection: 'row', alignItems: 'center', borderRadius: R.lg, padding: 18, marginTop: 18 },
  specGrid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  spec: { backgroundColor: C.card, borderRadius: R.md, padding: 12, marginBottom: 10, borderWidth: 1, borderColor: C.line2 },
  descCard: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  solidBar: { position: 'absolute', top: 0, left: 0, right: 0, backgroundColor: C.card, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  floatBar: { position: 'absolute', left: 0, right: 0, flexDirection: 'row', alignItems: 'center' },
  buyBar: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: C.card,
    paddingTop: 10,
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: C.line,
    elevation: 12,
    shadowColor: '#000',
    shadowOpacity: 0.08,
    shadowRadius: 10,
    shadowOffset: { width: 0, height: -3 },
  },
});
