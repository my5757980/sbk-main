/* THE ONLY DOOR TO DATA.

   Every screen asks this file for what it shows and never looks behind it.
   Phase 1 (now): the answers come from content.json, the website's own public
   content (tools/build-content.py), worked out with the website's own rules -
   the shop's filters, search and sorting below are SiteController@shop line
   for line. Phase 2: the same functions fetch the website's JSON API on the
   website's database, and no screen changes.

   Every function is async on purpose, so the screens are already written for
   a network. */
import content from './content.json';

/* Test entries on the live website (lorem-ipsum text, $360). They show on the
   website as well and should be deleted from its admin panel; the app leaves
   them out so a customer never sees them. */
const HIDDEN = new Set(['optio-et-accusantiu']);

/* SiteController::vehicleTypeLabel - the shop groups free-typed body types. */
export function typeLabel(type) {
  const t = String(type || '').trim().toLowerCase();
  if (!t) return null;
  if (t.includes('sedan')) return 'Sedan';
  if (t.includes('suv')) return 'SUV';
  if (t.includes('hatchback')) return 'Hatchback';
  if (t.includes('wagon')) return 'Wagon';
  if (t.includes('van') || t.includes('mpv')) return 'Van / MPV';
  if (t.includes('coupe')) return 'Coupe';
  if (t.includes('roadster') || t.includes('convertible')) return 'Convertible / Roadster';
  if (t.includes('bus')) return 'Bus';
  if (t.includes('truck') || t.includes('double cab')) return 'Truck / Double Cab';
  return 'Other';
}

/* SiteController::productGrade - the specification named "grade". */
function gradeOf(p) {
  const hit = (p.specs || []).find(([label]) => String(label).trim().toLowerCase() === 'grade');
  const g = hit ? String(hit[1]).trim() : '';
  return g || null;
}

const PRODUCTS = content.products
  .filter((p) => !HIDDEN.has(p.slug))
  .map((p, rank) => ({ ...p, rank, type: typeLabel(p.bodyType), grade: gradeOf(p) }));

const BY_SLUG = new Map(PRODUCTS.map((p) => [p.slug, p]));
const BLOGS = content.blogs || [];

const lower = (v) => String(v == null ? '' : v).toLowerCase();
const uniqueSorted = (list) => [...new Set(list.filter(Boolean))].sort((a, b) => String(a).localeCompare(String(b)));

/** Every choice the shop's filters offer, exactly as the website builds them. */
export async function getFilterOptions() {
  const years = [...new Set(PRODUCTS.map((p) => p.year).filter(Boolean))].sort((a, b) => b - a);
  return {
    brands: content.filters.brands,
    models: content.filters.models,
    types: uniqueSorted(PRODUCTS.map((p) => p.type)),
    grades: uniqueSorted(PRODUCTS.map((p) => p.grade)),
    years,
    maxPrice: Math.max(1, ...PRODUCTS.map((p) => Math.round(p.price))),
    total: PRODUCTS.length,
  };
}

/**
 * The shop: search, filter and sort - SiteController@shop.
 * f = { search, brands[], model, year, types[], grades[], maxMileage,
 *       transmission, minPrice, maxPrice, sort }
 */
export async function getProducts(f = {}) {
  const search = String(f.search || '').trim();
  let list = PRODUCTS;
  if (search) {
    const n = lower(search);
    list = list.filter((p) =>
      lower(p.title).includes(n) || lower(p.stockNo).includes(n) || lower(p.chassisNo).includes(n)
      || lower(p.brand).includes(n) || lower(p.model).includes(n) || lower(p.fuel).includes(n)
      || lower(p.bodyType).includes(n));
  }
  if (f.brands && f.brands.length) list = list.filter((p) => f.brands.includes(p.brand));
  if (f.model) list = list.filter((p) => p.model === f.model);
  if (f.year) list = list.filter((p) => p.year === Number(f.year));
  if (f.types && f.types.length) list = list.filter((p) => f.types.includes(p.type));
  if (f.grades && f.grades.length) list = list.filter((p) => f.grades.includes(p.grade));
  if (f.maxMileage) list = list.filter((p) => p.mileage != null && p.mileage <= Number(f.maxMileage));
  if (f.transmission) list = list.filter((p) => lower(p.transmission).includes(lower(f.transmission)));
  if (f.minPrice != null && f.minPrice !== '') list = list.filter((p) => p.price >= Number(f.minPrice));
  if (f.maxPrice != null && f.maxPrice !== '') list = list.filter((p) => p.price <= Number(f.maxPrice));

  const sorted = [...list];
  if (f.sort === 'price-low') sorted.sort((a, b) => a.price - b.price);
  else if (f.sort === 'price-high') sorted.sort((a, b) => b.price - a.price);
  else if (f.sort === 'year-new') sorted.sort((a, b) => (b.year || 0) - (a.year || 0));
  return { items: sorted, total: sorted.length };
}

export async function getProduct(slug) {
  return BY_SLUG.get(slug) || null;
}

/* Same brand or same model, newest first (the website takes three; a phone
   scrolls, so up to six). */
export async function getRelated(product, take = 6) {
  if (!product) return [];
  return PRODUCTS
    .filter((p) => p.slug !== product.slug && (p.brand === product.brand || (product.model && p.model === product.model)))
    .sort((a, b) => (b.id || 0) - (a.id || 0))
    .slice(0, take);
}

/** The search box's suggestions (SiteController@searchSuggestions): six at most. */
export async function suggest(q) {
  const n = lower(String(q || '').trim());
  if (n.length < 2) return [];
  return PRODUCTS
    .filter((p) => lower(p.title).includes(n) || lower(p.stockNo).includes(n) || lower(p.chassisNo).includes(n)
      || lower(p.fuel).includes(n) || lower(p.bodyType).includes(n) || lower(p.brand).includes(n) || lower(p.model).includes(n))
    .slice(0, 6);
}

/** Vehicles by their addresses, in the order given; ones no longer listed drop out. */
export async function getProductsBySlugs(slugs) {
  return (slugs || []).map((s) => BY_SLUG.get(s)).filter(Boolean);
}

/** The home page's three rows of vehicles, chosen in the website's admin panel. */
export async function getHome() {
  const pick = (slugs) => (slugs || []).map((s) => BY_SLUG.get(s)).filter(Boolean);
  return {
    bestSeller: pick(content.home.bestSeller),
    upcoming: pick(content.home.upcoming),
    newArrivals: pick(content.home.newArrivals),
    blogs: BLOGS.slice(0, 12),
    brands: brandCounts(),
  };
}

/** The stocked brands, with how many vehicles each has, most first. */
function brandCounts() {
  const counts = new Map();
  PRODUCTS.forEach((p) => counts.set(p.brand, (counts.get(p.brand) || 0) + 1));
  return [...counts.entries()].map(([name, count]) => ({ name, count })).sort((a, b) => b.count - a.count || a.name.localeCompare(b.name));
}

export async function getBrands() {
  return brandCounts();
}

/* ------------------------------------------------------------------ blog */

const blogText = (b) => [b.title, b.excerpt, ...(b.body || []).map((x) => x.text || (x.items || []).join(' ') || '')].join(' ');

export async function getBlogs({ search } = {}) {
  const n = lower(String(search || '').trim());
  const items = n ? BLOGS.filter((b) => lower(blogText(b)).includes(n)) : BLOGS;
  return { items, total: items.length };
}

export async function getBlog(slug) {
  return BLOGS.find((b) => b.slug === slug) || null;
}

export async function getRecentBlogs(exceptSlug, take = 4) {
  return BLOGS.filter((b) => b.slug !== exceptSlug).slice(0, take);
}

/** When the bundled content was copied from the website. */
export const CONTENT_DATE = content.builtAt;
