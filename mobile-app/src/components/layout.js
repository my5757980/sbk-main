/* How a screen is laid out: the page, its width on a tablet, the big heading
   block at the top of the website's pages, section headings, empty states. */
import { RefreshControl, ScrollView, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, R, lift, useUI } from '../theme';
import { Button, Eyebrow, Img, Press, T } from './base';
import { LOGO } from '../api/static';

/** A scrolling page on the app's grey, its content held in on a tablet. */
export function Page({ children, refreshing, onRefresh, bottom = 28, style, contentStyle, scrollRef, onScroll, white }) {
  return (
    <ScrollView
      ref={scrollRef}
      style={[{ flex: 1, backgroundColor: white ? C.card : C.bg }, style]}
      contentContainerStyle={[{ paddingBottom: bottom }, contentStyle]}
      keyboardShouldPersistTaps="handled"
      onScroll={onScroll}
      scrollEventThrottle={16}
      refreshControl={onRefresh ? <RefreshControl refreshing={!!refreshing} onRefresh={onRefresh} colors={[C.red]} tintColor={C.red} /> : undefined}
    >
      {children}
    </ScrollView>
  );
}

/** Content inside the side gutters; centred and width-capped on a tablet. */
export function Wrap({ children, style, flush }) {
  const ui = useUI();
  return (
    <View style={[{ width: ui.col, alignSelf: 'center', paddingHorizontal: flush ? 0 : ui.gutter }, style]}>
      {children}
    </View>
  );
}

/** The dark or red block that opens the website's inner pages. */
export function PageHero({ eyebrow, title, text, tone = 'dark', children, compact }) {
  const ui = useUI();
  return (
    <LinearGradient colors={tone === 'red' ? GRAD.redDeep : GRAD.dark} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}>
      <Wrap style={{ paddingTop: compact ? 22 : 30, paddingBottom: compact ? 24 : 34 }}>
        {eyebrow ? <Eyebrow color={tone === 'red' ? '#FFD5D6' : '#FF5A60'}>{eyebrow}</Eyebrow> : null}
        <T v="h1" color={C.white} size={ui.width < 360 ? 23 : 26}>{title}</T>
        {text ? <T v="body" color="rgba(255,255,255,0.78)" style={{ marginTop: 10 }}>{text}</T> : null}
        {children}
      </Wrap>
    </LinearGradient>
  );
}

/** A section's heading, with an optional "View all" on the right. */
export function SectionHead({ eyebrow, title, lead, action, onAction, center, light, style }) {
  return (
    <View style={[styles.head, center && { alignItems: 'center' }, style]}>
      <View style={[{ flex: 1 }, center && { alignItems: 'center' }]}>
        {eyebrow ? <Eyebrow center={center}>{eyebrow}</Eyebrow> : null}
        <T v="h2" color={light ? C.white : C.ink} center={center}>{title}</T>
        {lead ? <T v="sub" color={light ? 'rgba(255,255,255,0.75)' : C.muted} center={center} style={{ marginTop: 6 }}>{lead}</T> : null}
      </View>
      {action && !center ? (
        <Press onPress={onAction} style={styles.action} hitSlop={8} accessibilityLabel={action}>
          <T v="small" color={light ? C.white : C.red} weight="800">{action}</T>
          <Ionicons name="chevron-forward" size={15} color={light ? C.white : C.red} />
        </Press>
      ) : null}
    </View>
  );
}

/** A white card with the app's one shadow. */
export function Card({ children, style, padded = true }) {
  return <View style={[styles.card, padded && { padding: 16 }, style]}>{children}</View>;
}

/** Nothing to show yet - and what to do about it. */
export function Empty({ icon = 'car-sport-outline', title, text, action, onAction, second, onSecond, style }) {
  return (
    <View style={[styles.empty, style]}>
      <View style={styles.emptyIcon}>
        <Ionicons name={icon} size={30} color={C.red} />
      </View>
      <T v="h3" center>{title}</T>
      {text ? <T v="sub" color={C.muted} center style={{ marginTop: 8, maxWidth: 320 }}>{text}</T> : null}
      {action ? <Button title={action} onPress={onAction} style={{ marginTop: 20, minWidth: 220 }} /> : null}
      {second ? <Button title={second} kind="outline" onPress={onSecond} style={{ marginTop: 10, minWidth: 220 }} /> : null}
    </View>
  );
}

/** A plain-words note: the phase-1 notices, form results, warnings. */
export function Notice({ tone = 'preview', title, text, style }) {
  const t = {
    preview: [C.warnWash, C.warn, 'information-circle'],
    ok: [C.okWash, C.ok, 'checkmark-circle'],
    error: [C.redWash, C.red, 'alert-circle'],
    info: [C.line2, C.ink2, 'information-circle-outline'],
  }[tone];
  return (
    <View style={[styles.notice, { backgroundColor: t[0] }, style]}>
      <Ionicons name={t[2]} size={20} color={t[1]} style={{ marginRight: 10, marginTop: 1 }} />
      <View style={{ flex: 1 }}>
        {title ? <T v="title" size={13.5} color={t[1]} style={{ marginBottom: 2 }}>{title}</T> : null}
        <T v="sub" size={12.5} color={tone === 'preview' ? '#6E4A00' : t[1]}>{text}</T>
      </View>
    </View>
  );
}

/** The bar at the top of a tab: the SBK logo or a title, and actions. */
export function TabHeader({ title, logo, right, sub }) {
  const insets = useSafeAreaInsets();
  const ui = useUI();
  return (
    <View style={[styles.tabHead, { paddingTop: insets.top }]}>
      <View style={[styles.tabRow, { width: ui.col, alignSelf: 'center', paddingHorizontal: ui.gutter }]}>
        {logo ? (
          <Img src={LOGO} plain fit="contain" style={{ width: ui.s(112), height: ui.s(38) }} accessibilityLabel="SBK Global Auto Trading" />
        ) : (
          <View style={{ flex: 1 }}>
            <T v="h2" lines={1}>{title}</T>
            {sub ? <T v="small" color={C.muted} weight="600">{sub}</T> : null}
          </View>
        )}
        {logo ? <View style={{ flex: 1 }} /> : null}
        <View style={styles.tabActions}>{right}</View>
      </View>
    </View>
  );
}

/** A grey stand-in of the right shape while something loads. */
export function Skeleton({ style }) {
  return <View style={[{ backgroundColor: C.line2, borderRadius: R.md }, style]} />;
}

const styles = StyleSheet.create({
  head: { flexDirection: 'row', alignItems: 'flex-end', marginBottom: 14 },
  action: { flexDirection: 'row', alignItems: 'center', minHeight: 32, paddingLeft: 12 },
  card: { backgroundColor: C.card, borderRadius: R.lg, ...lift },
  empty: { alignItems: 'center', paddingHorizontal: 24, paddingVertical: 48 },
  emptyIcon: { width: 72, height: 72, borderRadius: 36, backgroundColor: C.redWash, alignItems: 'center', justifyContent: 'center', marginBottom: 16 },
  notice: { flexDirection: 'row', borderRadius: R.md, padding: 12 },
  tabHead: { backgroundColor: C.card, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  tabRow: { minHeight: 58, flexDirection: 'row', alignItems: 'center' },
  tabActions: { flexDirection: 'row', alignItems: 'center', columnGap: 4 },
});
