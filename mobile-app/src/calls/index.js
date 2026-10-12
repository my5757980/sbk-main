/* Where the call engine meets the phone - the SBK Chat app's calls.js, which
   was proven on real phones, pointed at this website's chat.

   engine.js is the website's calling code and knows nothing about phones. This
   hands it the three things a phone does differently:
     the microphone and camera   react-native-webrtc, asked for permission first
     the sound of a call         InCallManager - the phone's own ringtone, the
                                 ring-back tone, earpiece for voice, speaker for video
     the server                  the website's chat call endpoint, with this
                                 person's token (src/api/chat.js)

   Both native libraries are loaded on first use, inside a try: if either cannot
   start on some phone, the app still opens and a call says so in plain words. */
import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { PermissionsAndroid, Platform } from 'react-native';

import { createCallEngine } from './engine';
import * as chatApi from '../api/chat';

const EngineCtx = createContext(null);
const SnapCtx = createContext({ status: 'idle' });

let webrtcLib = null;
let webrtcErr = null;

function webrtc() {
  if (!webrtcLib && !webrtcErr) {
    try {
      // eslint-disable-next-line global-require
      webrtcLib = require('react-native-webrtc');
      if (!webrtcLib || !webrtcLib.RTCPeerConnection) throw new Error('react-native-webrtc loaded without RTCPeerConnection');
    } catch (e) {
      webrtcErr = e;
    }
  }
  if (webrtcErr) {
    const err = new Error('Calls are not available on this phone yet. Please send a message for now.');
    err.sbk = true;
    throw err;
  }
  return webrtcLib;
}

/** The video surface, or null when WebRTC could not start. */
export function rtcView() {
  try {
    return webrtc().RTCView;
  } catch (e) {
    return null;
  }
}

let incallLib;
function incall() {
  if (incallLib === undefined) {
    try {
      // eslint-disable-next-line global-require
      const m = require('react-native-incall-manager');
      incallLib = (m && m.default) || m || null;
    } catch (e) {
      incallLib = null;
    }
  }
  return incallLib;
}

async function askFor(wantVideo) {
  if (Platform.OS !== 'android') return;
  const want = [PermissionsAndroid.PERMISSIONS.RECORD_AUDIO];
  if (wantVideo) want.push(PermissionsAndroid.PERMISSIONS.CAMERA);
  const got = await PermissionsAndroid.requestMultiple(want);
  if (want.some((p) => got[p] !== PermissionsAndroid.RESULTS.GRANTED)) {
    throw new Error('permission');
  }
}

/* A ringtone that fails to play must never be the reason a call fails. */
function safe(fn) {
  return (...a) => {
    try {
      const m = incall();
      if (m) fn(m, ...a);
    } catch (e) { /* the call goes on without the sound */ }
  };
}

const audio = {
  outgoing: safe((m, kind) => { m.start({ media: kind === 'video' ? 'video' : 'audio', ringback: '_DTMF_' }); }),
  incoming: safe((m) => { m.startRingtone('_DEFAULT_', [0, 800, 1200], 'playback', -1); }),
  answered: safe((m, kind) => { m.stopRingtone(); m.start({ media: kind === 'video' ? 'video' : 'audio' }); }),
  connected: safe((m) => { m.stopRingback(); }),
  speaker: safe((m, on) => { m.setForceSpeakerphoneOn(!!on); }),
  end: safe((m) => { m.stopRingtone(); m.stopRingback(); m.stop(); }),
};

/* WebRTC is loaded only once a call is actually made or answered. */
const rtc = {
  get RTCPeerConnection() { return webrtc().RTCPeerConnection; },
  get RTCSessionDescription() { return webrtc().RTCSessionDescription; },
  mediaDevices: {
    getUserMedia: async (c) => {
      const lib = webrtc();
      await askFor(!!c.video);
      return lib.mediaDevices.getUserMedia(c);
    },
  },
};

/** `who` is anything that changes when a different person is in the chat. */
export function CallProvider({ who, children }) {
  const [snap, setSnap] = useState({ status: 'idle' });

  const engine = useMemo(() => {
    if (!who) return null;
    return createCallEngine({
      api: {
        ice: () => chatApi.iceServers(),
        start: (o) => chatApi.callStart(o),
        state: (id) => chatApi.callState(id),
        answer: (id, sdp) => chatApi.callAnswer(id, sdp),
        finish: (id, action, failed) => chatApi.callFinish(id, action, failed),
      },
      rtc,
      audio,
      onChange: setSnap,
    });
  }, [who]);

  // A different person in the chat puts any call down first.
  useEffect(() => () => {
    if (engine && engine.busy()) engine.hangUp();
  }, [engine]);

  return (
    <EngineCtx.Provider value={engine}>
      <SnapCtx.Provider value={snap}>{children}</SnapCtx.Provider>
    </EngineCtx.Provider>
  );
}

export function useCallEngine() {
  return useContext(EngineCtx);
}

export function useCallSnap() {
  return useContext(SnapCtx);
}

/** The chat's "ringing" hint (ping) in the shape the engine's notePoll reads. */
export function ringingAsCall(r) {
  if (!r || !r.id) return null;
  return { id: r.id, kind: r.kind, state: 'ringing', incoming: true, peer: r.from || '', peerId: 0, thread: r.thread || 0 };
}
