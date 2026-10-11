/* The website this app belongs to.

   Every vehicle and blog picture is the website's own file, and in phase 2 the
   app reads all of its data from the website's API, so the address is set at
   BUILD time, the same code serving today's address and the final domain:
       EXPO_PUBLIC_SITE_URL=https://<the website> npx expo prebuild ...
   Today the website runs at v2.sbkautotrading.com. */
export const SITE_URL = String(process.env.EXPO_PUBLIC_SITE_URL || 'https://v2.sbkautotrading.com').replace(/\/+$/, '');

/** A path on the website ("/uploads/front/x.webp") as a full address. */
export function siteUrl(path) {
  if (!path) return '';
  if (/^https?:\/\//i.test(path)) return path;
  return SITE_URL + (path.startsWith('/') ? '' : '/') + path;
}

/* The contact details at the top of every website page. */
export const CONTACT = {
  phone: '+81 70-9427-5106',
  dial: '+817094275106',
  email: 'info@sbkautotrading.com',
  paymentEmail: 'payment@sbkautotrading.com',
  whatsapp: '817094275106',
  hours: 'Mon - Fri: 10:00 - 18:00',
};
