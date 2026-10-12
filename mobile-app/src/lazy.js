/* Native libraries a chat feature needs, loaded the first time the feature is
   used rather than when the app starts (the SBK Chat app's lesson: a library
   that cannot start on some phone then costs only that one feature - a voice
   note or a picture - and the app stays open).

   The answer is kept for the life of the app, so a screen that calls a hook
   from one of these calls it on every render or on none. */
let audioMod;
let pickerMod;

/** expo-audio (voice notes: record and play), or null if it cannot start here. */
export function audioLib() {
  if (audioMod === undefined) {
    try {
      // eslint-disable-next-line global-require
      audioMod = require('expo-audio');
    } catch (e) {
      audioMod = null;
    }
  }
  return audioMod;
}

/** expo-image-picker (pictures), or null if it cannot start here. */
export function pickerLib() {
  if (pickerMod === undefined) {
    try {
      // eslint-disable-next-line global-require
      pickerMod = require('expo-image-picker');
    } catch (e) {
      pickerMod = null;
    }
  }
  return pickerMod;
}
