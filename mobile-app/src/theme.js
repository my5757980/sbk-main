import { PixelRatio, Platform, useWindowDimensions } from 'react-native';

/* The website's own colours (public/assets/front/css/home.css and the pages'
   styles): SBK red with its darker gradient end, near-black, white cards on a
   light grey page. */
export const C = {
  red: '#D71920',
  redDark: '#A90F15',
  redDeep: '#7E0A0F',
  redWash: '#FDECEC',
  redLine: '#F5C6C8',

  ink: '#111216',
  ink2: '#3A3F47',
  muted: '#6B717B',
  faint: '#9AA0A8',

  line: '#E4E6EA',
  line2: '#EFF0F3',
  field: '#FAFBFC',
  bg: '#F4F5F7',
  card: '#FFFFFF',
  white: '#FFFFFF',

  dark: '#0B0C0F',
  dark2: '#1A1C22',
  dark3: '#2A2D35',

  ok: '#1F9254',
  okWash: '#E7F5EC',
  warn: '#9A6400',
  warnWash: '#FFF5E0',
};

export const GRAD = {
  red: ['#D71920', '#A90F15'],
  redDeep: ['#C3161D', '#7E0A0F'],
  dark: ['#1A1C22', '#0B0C0F'],
  shade: ['rgba(11,12,15,0.15)', 'rgba(11,12,15,0.55)', 'rgba(11,12,15,0.92)'],
};

export const R = { xs: 6, sm: 8, md: 12, lg: 16, xl: 22, pill: 999 };

/* HOW THE APP STAYS THE RIGHT SIZE ON EVERY PHONE (the same rules as the SBK
   Chat app, which were measured at nine widths): everything is drawn for a
   390pt phone and scaled by this screen's width, clamped so a 320pt phone
   stays readable and a tablet shows more rather than bigger; no text under
   11pt; no touch target under 44pt; and the phone's own text-size setting is
   honoured but capped, so it cannot push buttons off the screen. */
export const BASE_W = 390;
export const MIN_TEXT = 11;
export const MAX_FONT = 1.25;

export function scaleFor(width) {
  return Math.max(0.92, Math.min(1.12, width / BASE_W));
}

export function useUI() {
  const { width, height } = useWindowDimensions();
  const k = scaleFor(width);
  const tablet = width >= 700;
  // The column content is held in on anything wider than a big phone.
  const col = tablet ? Math.min(980, width - 48) : width;
  const gutter = width < 360 ? 14 : tablet ? 24 : 16;
  return {
    width,
    height,
    tablet,
    landscape: width > height,
    col,
    gutter,
    /** Content width inside the gutters. */
    inner: col - gutter * 2,
    /** Where content starts from the screen's left edge (side-scrolling rows use it). */
    edge: (width - col) / 2 + gutter,
    s: (n) => Math.round(PixelRatio.roundToNearestPixel(n * k)),
    t: (n) => Math.max(MIN_TEXT, Math.round(PixelRatio.roundToNearestPixel(n * k))),
    tap: (n) => Math.max(44, Math.round((n || 48) * k)),
    /** Columns of vehicle cards that fit this screen. */
    cols: width >= 1000 ? 4 : width >= 700 ? 3 : width >= 340 ? 2 : 1,
  };
}

/* The type scale. A handful of sizes carries the whole app, which is what makes
   it read as one designed thing. Headings are the website's heavy display type. */
export const TYPE = {
  display: { size: 30, weight: '900', line: 34 },
  h1: { size: 25, weight: '900', line: 30 },
  h2: { size: 21, weight: '800', line: 26 },
  h3: { size: 17, weight: '800', line: 22 },
  title: { size: 15, weight: '700', line: 20 },
  body: { size: 14.5, weight: '400', line: 21 },
  sub: { size: 13, weight: '400', line: 19 },
  small: { size: 12, weight: '600', line: 16 },
  eyebrow: { size: 11, weight: '900', line: 14, upper: true, spacing: 1.4 },
  label: { size: 11, weight: '800', line: 14, upper: true, spacing: 0.8 },
};

/* One shadow for things that sit on the page, one for things that float. */
export const lift = Platform.select({
  ios: { shadowColor: '#111216', shadowOpacity: 0.08, shadowRadius: 14, shadowOffset: { width: 0, height: 6 } },
  android: { elevation: 3 },
  default: {},
});

export const float = Platform.select({
  ios: { shadowColor: '#111216', shadowOpacity: 0.18, shadowRadius: 22, shadowOffset: { width: 0, height: 10 } },
  android: { elevation: 10 },
  default: {},
});
