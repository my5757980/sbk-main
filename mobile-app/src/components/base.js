/* The pieces every screen is built from: text, buttons, pictures, badges.
   One set, used everywhere, so the app reads as one designed thing. */
import { useState } from 'react';
import { ActivityIndicator, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { Image } from 'expo-image';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, MAX_FONT, R, TYPE, useUI } from '../theme';
import { siteUrl } from '../config';

/* ------------------------------------------------------------------ text */

export function T({ v = 'body', color, size, weight, center, upper, lines, style, children, ...rest }) {
  const ui = useUI();
  const t = TYPE[v] || TYPE.body;
  const fs = ui.t(size || t.size);
  const lh = Math.round(fs * (t.line / t.size));
  return (
    <Text
      maxFontSizeMultiplier={MAX_FONT}
      numberOfLines={lines}
      style={[
        {
          fontSize: fs,
          lineHeight: lh,
          fontWeight: weight || t.weight,
          color: color || C.ink,
          letterSpacing: t.spacing || 0,
          includeFontPadding: false,
        },
        (upper ?? t.upper) && { textTransform: 'uppercase' },
        center && { textAlign: 'center' },
        style,
      ]}
      {...rest}
    >
      {children}
    </Text>
  );
}

/* --------------------------------------------------------------- pressing */

/** Anything tappable: a ripple on Android, a dim on iPhone. */
export function Press({ onPress, style, children, ripple = 'rgba(17,18,22,0.08)', disabled, hitSlop, accessibilityLabel, borderless }) {
  return (
    <Pressable
      onPress={onPress}
      disabled={disabled}
      hitSlop={hitSlop}
      accessibilityRole="button"
      accessibilityLabel={accessibilityLabel}
      android_ripple={{ color: ripple, borderless: !!borderless }}
      style={({ pressed }) => [style, Platform.OS === 'ios' && pressed && { opacity: 0.72 }, disabled && { opacity: 0.5 }]}
    >
      {children}
    </Pressable>
  );
}

/* ---------------------------------------------------------------- buttons */

const KINDS = {
  primary: { bg: null, grad: GRAD.red, fg: C.white, border: null },
  dark: { bg: C.ink, fg: C.white, border: null },
  outline: { bg: C.white, fg: C.ink, border: C.line },
  light: { bg: C.white, fg: C.red, border: null },
  ghostLight: { bg: 'rgba(255,255,255,0.12)', fg: C.white, border: 'rgba(255,255,255,0.35)' },
  soft: { bg: C.redWash, fg: C.red, border: null },
  whatsapp: { bg: '#1FAF5A', fg: C.white, border: null },
};

export function Button({ title, onPress, kind = 'primary', size = 'md', icon, iconRight, full, disabled, loading, style }) {
  const ui = useUI();
  const k = KINDS[kind] || KINDS.primary;
  const h = size === 'sm' ? Math.max(44, ui.s(42)) : ui.tap(50);
  const inner = (
    <View style={[styles.btnInner, { minHeight: h, paddingHorizontal: size === 'sm' ? 14 : 20 }]}>
      {loading ? (
        <ActivityIndicator color={k.fg} />
      ) : (
        <>
          {icon ? <Ionicons name={icon} size={ui.s(size === 'sm' ? 16 : 18)} color={k.fg} style={{ marginRight: 8 }} /> : null}
          <T v="title" color={k.fg} size={size === 'sm' ? 13 : 14.5} weight="800" lines={1} style={{ letterSpacing: 0.2 }}>
            {title}
          </T>
          {iconRight ? <Ionicons name={iconRight} size={ui.s(17)} color={k.fg} style={{ marginLeft: 8 }} /> : null}
        </>
      )}
    </View>
  );
  return (
    <View style={[styles.btnWrap, full && { alignSelf: 'stretch' }, { borderRadius: R.md }, k.border && { borderWidth: 1.5, borderColor: k.border }, style]}>
      <Press
        onPress={onPress}
        disabled={disabled || loading}
        ripple={kind === 'outline' || kind === 'light' ? 'rgba(17,18,22,0.08)' : 'rgba(255,255,255,0.22)'}
        style={{ borderRadius: R.md, overflow: 'hidden', backgroundColor: k.bg || undefined }}
        accessibilityLabel={title}
      >
        {k.grad ? (
          <LinearGradient colors={k.grad} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }}>
            {inner}
          </LinearGradient>
        ) : inner}
      </Press>
    </View>
  );
}

/** A round icon button, with an optional count badge. */
export function IconButton({ name, onPress, kind = 'plain', size = 44, color, badge, label, style }) {
  const ui = useUI();
  const d = Math.max(40, ui.s(size));
  const bg = { plain: 'transparent', soft: C.white, dark: 'rgba(11,12,15,0.48)', red: C.red, wash: C.line2 }[kind];
  const fg = color || (kind === 'dark' || kind === 'red' ? C.white : C.ink);
  return (
    <View style={[{ width: d, height: d }, style]}>
      <Press
        onPress={onPress}
        accessibilityLabel={label || name}
        hitSlop={6}
        style={[styles.iconBtn, { width: d, height: d, borderRadius: d / 2, backgroundColor: bg }, kind === 'soft' && styles.softShadow]}
        ripple={kind === 'dark' || kind === 'red' ? 'rgba(255,255,255,0.25)' : 'rgba(17,18,22,0.1)'}
      >
        <Ionicons name={name} size={Math.round(d * 0.5)} color={fg} />
      </Press>
      {badge ? <Badge count={badge} style={styles.iconBadge} /> : null}
    </View>
  );
}

export function Badge({ count, style }) {
  if (!count) return null;
  return (
    <View style={[styles.badge, style]} pointerEvents="none">
      <T v="small" size={10.5} color={C.white} weight="800" style={{ lineHeight: 14 }}>
        {count > 99 ? '99+' : String(count)}
      </T>
    </View>
  );
}

/* --------------------------------------------------------------- pictures */

/** A picture: a website path ("/uploads/...") or one carried in the app. */
export function Img({ src, style, fit = 'cover', plain, priority, ...rest }) {
  const [failed, setFailed] = useState(false);
  const source = typeof src === 'string' ? { uri: siteUrl(src) } : src;
  if (!src || failed) {
    return (
      <View style={[style, styles.noImg]}>
        <Ionicons name="car-sport-outline" size={28} color={C.faint} />
      </View>
    );
  }
  return (
    <Image
      source={source}
      style={[!plain && { backgroundColor: C.line2 }, style]}
      contentFit={fit}
      transition={160}
      cachePolicy="memory-disk"
      priority={priority}
      recyclingKey={typeof src === 'string' ? src : undefined}
      onError={() => setFailed(true)}
      {...rest}
    />
  );
}

/* ------------------------------------------------------------- small bits */

export function Eyebrow({ children, color = C.red, style, center }) {
  return <T v="eyebrow" color={color} center={center} style={[{ marginBottom: 6 }, style]}>{children}</T>;
}

export function Pill({ text, tone = 'red', style }) {
  const tones = {
    red: [C.red, C.white],
    dark: [C.ink, C.white],
    light: ['rgba(255,255,255,0.16)', C.white],
    wash: [C.redWash, C.red],
    grey: [C.line2, C.ink2],
    ok: [C.okWash, C.ok],
  };
  const [bg, fg] = tones[tone] || tones.red;
  return (
    <View style={[styles.pill, { backgroundColor: bg }, style]}>
      <T v="label" size={10.5} color={fg} lines={1} style={{ letterSpacing: 0.6 }}>{text}</T>
    </View>
  );
}

export function Divider({ style }) {
  return <View style={[{ height: StyleSheet.hairlineWidth, backgroundColor: C.line }, style]} />;
}

const styles = StyleSheet.create({
  btnWrap: { overflow: 'hidden' },
  btnInner: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center' },
  iconBtn: { alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  softShadow: Platform.select({
    ios: { shadowColor: '#111216', shadowOpacity: 0.14, shadowRadius: 8, shadowOffset: { width: 0, height: 3 } },
    android: { elevation: 3 },
    default: {},
  }),
  badge: {
    minWidth: 18,
    height: 18,
    paddingHorizontal: 4,
    borderRadius: 9,
    backgroundColor: C.red,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1.5,
    borderColor: C.white,
  },
  iconBadge: { position: 'absolute', top: -2, right: -2 },
  noImg: { backgroundColor: C.line2, alignItems: 'center', justifyContent: 'center' },
  pill: { alignSelf: 'flex-start', paddingHorizontal: 9, paddingVertical: 5, borderRadius: R.pill },
});
