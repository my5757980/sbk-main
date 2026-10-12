/* THE ONLY DOOR TO DATA.

   Every screen asks this file for what it shows and never looks behind it.
   The answers come from the website's own database, through the website's
   API for the app (sbk-main routes/app.php): the same vehicles, blogs,
   accounts, orders and chat the website's pages use.

   When the website cannot be reached (no connection, or a website that does
   not carry the app's API yet), the reading screens fall back to the copy of
   the website's content the app carries (./bundled.js), so the app never opens
   empty. Signing in, orders, the contact form and the chat need the website. */
import * as bundled from './bundled';
import { ApiError, request } from './http';

export { ApiError } from './http';
export const CONTENT_DATE = bundled.CONTENT_DATE;

/* Is the website's API there? Checked with the filter lists (which every shop
   screen needs anyway), and again every few minutes or on pull-to-refresh. */
let mode = null;
let checkedAt = 0;
let filtersCache = null;

async function live(force) {
  if (!force && mode && Date.now() - checkedAt < 3 * 60 * 1000) return mode === 'live';
  try {
    const f = await request('GET', '/filters', { timeout: 10000 });
    if (!f || !Array.isArray(f.brands)) throw new ApiError('Not the app API', 0);
    filtersCache = f;
    mode = 'live';
  } catch (e) {
    mode = 'bundled';
  }
  checkedAt = Date.now();
  return mode === 'live';
}

/** Whether what is on screen is the website's database (true) or the app's copy. */
export function isLive() {
  return mode === 'live';
}

/** Pull-to-refresh: look for the website again now. */
export async function refresh() {
  return live(true);
}

/* A read that falls back to the app's copy if the website drops out midway. */
async function read(fromApi, fromCopy) {
  if (await live()) {
    try {
      return await fromApi();
    } catch (e) {
      if (!(e instanceof ApiError) || !e.offline || e.status === 404) throw e;
      mode = 'bundled';
    }
  }
  return fromCopy();
}

/* The app's filter object -> the website's own shop parameters. */
function shopQuery(f = {}) {
  return {
    search: f.search,
    brands: f.brands,
    model: f.model,
    year: f.year,
    types: f.types,
    grades: f.grades,
    max_mileage: f.maxMileage,
    transmission: f.transmission,
    min_price: f.minPrice,
    max_price: f.maxPrice,
    sort: f.sort && f.sort !== 'default' ? f.sort : undefined,
  };
}

/* ------------------------------------------------------------- the shop */

export async function getFilterOptions() {
  return read(async () => filtersCache || (filtersCache = await request('GET', '/filters')), () => bundled.getFilterOptions());
}

/** The shop, a page at a time: { items, total, page, lastPage }. */
export async function getProducts(f = {}, page = 1, perPage = 20) {
  return read(
    () => request('GET', '/products', { query: { ...shopQuery(f), page, per_page: perPage } }),
    async () => {
      const all = await bundled.getProducts(f);
      return {
        items: all.items.slice((page - 1) * perPage, page * perPage),
        total: all.total,
        page,
        lastPage: Math.max(1, Math.ceil(all.total / perPage)),
      };
    },
  );
}

/** A vehicle, or null when it is no longer listed. */
export async function getProduct(slug) {
  try {
    return await read(() => request('GET', '/products/' + encodeURIComponent(slug)), () => bundled.getProduct(slug));
  } catch (e) {
    if (e instanceof ApiError && e.status === 404) return null;
    throw e;
  }
}

/** Related vehicles: the website sends them with the vehicle. */
export async function getRelated(product) {
  if (product && Array.isArray(product.related) && product.related.length && typeof product.related[0] === 'object') {
    return product.related;
  }
  return bundled.getRelated(product);
}

/** The phone's cart and wishlist, as vehicles still listed, in the same order. */
export async function getProductsBySlugs(slugs) {
  if (!slugs || !slugs.length) return [];
  return read(
    async () => (await request('GET', '/products', { query: { slugs: slugs.join(',') } })).items,
    () => bundled.getProductsBySlugs(slugs),
  );
}

export async function getHome() {
  return read(() => request('GET', '/home'), () => bundled.getHome());
}

/* ----------------------------------------------------------------- blog */

const blogCache = new Map();

export async function getBlogs({ search } = {}) {
  return read(() => request('GET', '/blogs', { query: { search, per_page: 50 } }), () => bundled.getBlogs({ search }));
}

export async function getBlog(slug) {
  try {
    const b = await read(() => request('GET', '/blogs/' + encodeURIComponent(slug)), () => bundled.getBlog(slug));
    if (b) blogCache.set(slug, b);
    return b;
  } catch (e) {
    if (e instanceof ApiError && e.status === 404) return null;
    throw e;
  }
}

export async function getRecentBlogs(exceptSlug, take = 4) {
  const b = blogCache.get(exceptSlug);
  if (b && Array.isArray(b.recent)) return b.recent.slice(0, take);
  return bundled.getRecentBlogs(exceptSlug, take);
}

/* ------------------------------------------- what needs the website itself */

export async function login(email, password) {
  return request('POST', '/auth/login', { json: { email, password, device_name: 'SBK app' } });
}

export async function register(fields) {
  return request('POST', '/auth/register', {
    json: {
      name: fields.name,
      email: fields.email,
      phone: fields.phone,
      country: fields.country,
      password: fields.password,
      password_confirmation: fields.confirm,
      device_name: 'SBK app',
    },
  });
}

export async function forgotPassword(email) {
  return request('POST', '/auth/forgot', { json: { email } });
}

export async function logout() {
  return request('POST', '/auth/logout');
}

export async function me() {
  return (await request('GET', '/me')).user;
}

/** Edit profile; `photo` is a picked image { uri, mimeType, fileName } or null. */
export async function updateProfile(fields, photo) {
  const form = new FormData();
  ['name', 'email', 'phone', 'country'].forEach((k) => form.append(k, fields[k] || ''));
  if (photo && photo.uri) {
    form.append('profile_picture', {
      uri: photo.uri,
      name: photo.fileName || 'profile.jpg',
      type: photo.mimeType || 'image/jpeg',
    });
  }
  return request('POST', '/me', { form, timeout: 60000 });
}

export async function changePassword(current, next, confirm) {
  return request('PUT', '/me/password', { json: { current_password: current, password: next, password_confirmation: confirm } });
}

export async function getOrders() {
  return (await request('GET', '/orders')).orders;
}

/** The website's "Submit Order for Checking". */
export async function checkout(slugs, form, percent, amount) {
  return request('POST', '/checkout', {
    json: {
      items: slugs,
      name: form.name,
      email: form.email,
      phone: form.phone,
      country: form.country,
      city: form.city,
      port: form.port,
      message: form.message,
      payment_percentage: percent,
      payment_amount: amount || undefined,
      password: form.password || undefined,
      password_confirmation: form.confirm || undefined,
    },
  });
}

/** PayPal / card: the website keeps the checkout and returns its payment page. */
export async function startPay(slugs, form, percent, amount) {
  return request('POST', '/checkout/pay', {
    json: {
      items: slugs,
      name: form.name,
      email: form.email,
      phone: form.phone,
      country: form.country,
      city: form.city,
      port: form.port,
      message: form.message,
      payment_percentage: percent,
      payment_amount: amount || undefined,
      password: form.password || undefined,
      password_confirmation: form.confirm || undefined,
    },
  });
}

export async function sendContact(form) {
  return request('POST', '/contact', { json: form });
}
