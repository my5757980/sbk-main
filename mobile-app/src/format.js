/* Numbers the way the website prints them. Written by hand rather than with
   toLocaleString, which a phone's JavaScript engine may not do the same way. */

/** 132000 -> "132,000" */
export function grouped(n) {
  const v = Math.round(Number(n) || 0);
  const s = String(Math.abs(v)).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return (v < 0 ? '-' : '') + s;
}

/** 8200 -> "$8,200.00", the website's price format. */
export function money(n) {
  const v = Number(n) || 0;
  const cents = Math.round(Math.abs(v) * 100);
  const whole = Math.floor(cents / 100);
  const rest = String(cents % 100).padStart(2, '0');
  return (v < 0 ? '-' : '') + '$' + grouped(whole) + '.' + rest;
}

/** A vehicle's mileage, as the website's mileage_label: "132,000 km" or "N/A". */
export function mileageLabel(p) {
  if (p && p.mileage) return grouped(p.mileage) + ' km';
  return (p && p.mileageLabel) || 'N/A';
}

/** The local time at an office, from its time zone ("Asia/Tokyo" -> "14:05"). */
export function clockIn(zone, now) {
  const at = now || new Date();
  try {
    const f = new Intl.DateTimeFormat('en-GB', { timeZone: zone, hour: '2-digit', minute: '2-digit', hour12: false });
    const out = f.format(at);
    if (/^\d{2}:\d{2}$/.test(out)) return out;
  } catch (e) {
    // Fall through to the fixed offsets below.
  }
  const fixed = { 'Asia/Dubai': 4, 'Asia/Tokyo': 9, 'Africa/Nairobi': 3, 'America/Jamaica': -5, 'Asia/Bangkok': 7, 'Asia/Karachi': 5 };
  let offset = fixed[zone];
  if (zone === 'America/Chicago') offset = usDaylight(at) ? -5 : -6;
  if (offset === undefined) return '';
  const t = new Date(at.getTime() + offset * 3600000);
  return String(t.getUTCHours()).padStart(2, '0') + ':' + String(t.getUTCMinutes()).padStart(2, '0');
}

/* US daylight time: second Sunday of March to first Sunday of November, 2am. */
function usDaylight(d) {
  const y = d.getUTCFullYear();
  const nthSunday = (month, n) => {
    const first = new Date(Date.UTC(y, month, 1));
    return 1 + ((7 - first.getUTCDay()) % 7) + (n - 1) * 7;
  };
  const start = Date.UTC(y, 2, nthSunday(2, 2), 8);
  const end = Date.UTC(y, 10, nthSunday(10, 1), 7);
  const t = d.getTime();
  return t >= start && t < end;
}

/** "+971 55 417 7311 / +971 ..." -> the first number, dialable. */
export function dialable(phone) {
  const first = String(phone || '').split('/')[0];
  return first.replace(/[^0-9+]/g, '');
}

export function initials(name) {
  const p = String(name || '').trim().split(/\s+/).filter(Boolean);
  if (!p.length) return 'SBK';
  return ((p[0][0] || '') + (p.length > 1 ? p[p.length - 1][0] : '')).toUpperCase();
}
