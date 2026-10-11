/* Leaving the app for the phone's own apps: WhatsApp, the dialler, e-mail,
   maps, the share sheet - the same links the website uses. */
import { Alert, Linking, Share } from 'react-native';

import { CONTACT, siteUrl } from './config';
import { dialable, money } from './format';

function open(url) {
  Linking.openURL(url).catch(() => Alert.alert('Could not open', 'No app on this phone can open this link.'));
}

export function whatsApp(text) {
  open('https://wa.me/' + CONTACT.whatsapp + (text ? '?text=' + encodeURIComponent(text) : ''));
}

export function callNumber(phone) {
  open('tel:' + dialable(phone || CONTACT.dial));
}

export function emailTo(address, subject) {
  open('mailto:' + (address || CONTACT.email) + (subject ? '?subject=' + encodeURIComponent(subject) : ''));
}

export function openMap(query) {
  open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(query));
}

export function openSite(path) {
  open(siteUrl(path));
}

export function shareProduct(p) {
  const url = siteUrl('/product/' + p.slug);
  Share.share({ message: p.title + ' - ' + money(p.price) + ' (FOB)\n' + url, url, title: p.title }).catch(() => {});
}

export function shareBlog(b) {
  const url = siteUrl('/blog/' + b.slug);
  Share.share({ message: b.title + '\n' + url, url, title: b.title }).catch(() => {});
}
