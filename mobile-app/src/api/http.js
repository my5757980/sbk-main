/* Talking to the website: /api/app/* on the website's own database
   (sbk-main routes/app.php).

   A signed-in customer's token goes with every call ("Authorization: Bearer"),
   and a chat visitor's token with chat calls ("X-Chat-Guest"). Errors come back
   as ApiError with the website's own words where it gave some. */
import { SITE_URL } from '../config';

let authToken = null;
let guestToken = null;

export function setAuthToken(token) {
  authToken = token || null;
}

export function setGuestToken(token) {
  guestToken = token || null;
}

export function hasAuth() {
  return !!authToken;
}

export class ApiError extends Error {
  constructor(message, status, errors) {
    super(message);
    this.status = status;
    this.errors = errors || null;
  }

  /** The website could not be reached, or does not carry the app's API yet. */
  get offline() {
    return this.status === 0 || this.status === 404 || this.status >= 500;
  }
}

const NO_CONNECTION = 'Could not reach SBK. Please check your internet connection and try again.';
const NOT_READY = 'This service is not available on SBK right now. Please try again later.';

/** "brands[]=Toyota&brands[]=BMW&sort=price-low" from { brands: [...], sort }. */
export function queryOf(params) {
  const parts = [];
  Object.keys(params || {}).forEach((key) => {
    const v = params[key];
    if (v == null || v === '') return;
    if (Array.isArray(v)) {
      v.filter((x) => x != null && x !== '').forEach((x) => parts.push(encodeURIComponent(key + '[]') + '=' + encodeURIComponent(x)));
    } else {
      parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(v));
    }
  });
  return parts.join('&');
}

function firstError(data) {
  if (!data || typeof data !== 'object') return '';
  if (data.errors && typeof data.errors === 'object') {
    const key = Object.keys(data.errors)[0];
    const list = key ? data.errors[key] : null;
    if (Array.isArray(list) && list[0]) return String(list[0]);
  }
  return String(data.error || data.message || '');
}

/**
 * One call to the website. opts: { query, json, form, timeout, chat }.
 * `form` is a FormData (pictures); React Native sets its boundary itself.
 */
export async function request(method, path, opts = {}) {
  const { query, json, form, urlencoded, timeout = 20000, chat = false } = opts;
  const qs = typeof query === 'string' ? query : queryOf(query);
  const url = SITE_URL + '/api/app' + path + (qs ? '?' + qs : '');
  const headers = { Accept: 'application/json' };
  if (authToken) headers.Authorization = 'Bearer ' + authToken;
  if (chat && guestToken && !authToken) headers['X-Chat-Guest'] = guestToken;
  let body;
  if (json) {
    headers['Content-Type'] = 'application/json';
    body = JSON.stringify(json);
  } else if (urlencoded) {
    // As a browser posts a form - the chat's call endpoint reads the SDP from it.
    headers['Content-Type'] = 'application/x-www-form-urlencoded';
    body = queryOf(urlencoded);
  } else if (form) {
    body = form;
  }

  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), timeout);
  let res;
  try {
    res = await fetch(url, { method, headers, body, signal: controller.signal });
  } catch (e) {
    throw new ApiError(NO_CONNECTION, 0);
  } finally {
    clearTimeout(timer);
  }

  let data = null;
  try {
    data = await res.json();
  } catch (e) {
    data = null;
  }
  // A page that is not the website's answer (a hotel Wi-Fi sign-in, an error
  // page) counts as no connection.
  if (res.ok && (data === null || typeof data !== 'object')) {
    throw new ApiError(NO_CONNECTION, 0);
  }
  if (!res.ok) {
    // The framework's words for developers ("The route api/app/contact could
    // not be found.", "No query results for model ...") never reach a customer.
    let message = firstError(data);
    if (res.status === 429) message = 'Too many tries. Please wait a minute and try again.';
    else if (res.status === 401 && (!message || message === 'Unauthenticated.')) message = 'Please sign in again.';
    else if (res.status === 405 || /^The route .* could not be found/i.test(message)) message = NOT_READY;
    else if (/^No query results/i.test(message)) message = 'Not found.';
    else if (res.status === 413) message = 'This file is too large to send.';
    else if (res.status >= 500 || !message || message === 'Server Error') message = res.status === 404 ? 'Not found.' : NO_CONNECTION;
    throw new ApiError(message, res.status, data && data.errors);
  }
  return data;
}

/** A picture or voice note from the chat: the website hands it only to its owner. */
export function mediaSource(id) {
  const headers = {};
  if (authToken) headers.Authorization = 'Bearer ' + authToken;
  else if (guestToken) headers['X-Chat-Guest'] = guestToken;
  return { uri: SITE_URL + '/api/app/chat/media/' + id, headers };
}
