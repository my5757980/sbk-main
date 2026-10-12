/* The call, on the whole screen - over whatever was open, the way a phone's own
   call screen comes up over any app (the SBK Chat app's call screen, in this
   app's colours).

   Voice: the other person's initials in the middle, a slow pulse while it
   rings. Video: their picture fills the screen and yours sits in the corner.
   The words ("Ringing…", "Connecting…", the clock, "No answer") come from the
   engine, which is the website chat's own calling code. */
import { useEffect, useRef } from 'react';
import { Animated, BackHandler, Easing, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, useUI } from '../theme';
import { LOGO } from '../api/static';
import { Img, Press, T } from '../components/base';
import { rtcView, useCallEngine, useCallSnap } from '../calls';
import { initials } from '../format';

const FACES = ['#D71920', '#2A2D35', '#A90F15', '#3A3F47', '#7E0A0F', '#1A1C22'];

function Round({ icon, label, onPress, tone = 'glass', size, active, rotate }) {
  const bg = tone === 'end' ? C.red : tone === 'go' ? '#1FAF5A' : active ? C.white : 'rgba(255,255,255,0.16)';
  const ink = active && tone === 'glass' ? C.ink : C.white;
  return (
    <View style={{ alignItems: 'center', width: size + 22 }}>
      <Press onPress={onPress} style={{ width: size, height: size, borderRadius: size / 2, backgroundColor: bg, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' }} ripple="rgba(255,255,255,0.25)" accessibilityLabel={label}>
        <Ionicons name={icon} size={size * 0.42} color={ink} style={rotate ? { transform: [{ rotate: '135deg' }] } : null} />
      </Press>
      <T v="small" size={11.5} weight="600" color="#D9DCE2" lines={1} style={{ marginTop: 7 }}>{label}</T>
    </View>
  );
}

export default function CallScreen() {
  const ui = useUI();
  const inset = useSafeAreaInsets();
  const snap = useCallSnap();
  const calls = useCallEngine();
  const pulse = useRef(new Animated.Value(0)).current;

  const showing = snap.status && snap.status !== 'idle';
  const ringing = snap.status === 'incoming' || (snap.status === 'outgoing' && snap.id);

  useEffect(() => {
    if (!ringing) {
      pulse.stopAnimation();
      pulse.setValue(0);
      return undefined;
    }
    const loop = Animated.loop(Animated.timing(pulse, { toValue: 1, duration: 1600, easing: Easing.out(Easing.quad), useNativeDriver: true }));
    loop.start();
    return () => loop.stop();
  }, [ringing, pulse]);

  // Back does not drop a call by accident; the red button does.
  useEffect(() => {
    if (!showing) return undefined;
    const sub = BackHandler.addEventListener('hardwareBackPress', () => true);
    return () => sub.remove();
  }, [showing]);

  if (!showing || !calls) return null;

  const RTCView = snap.kind === 'video' ? rtcView() : null;
  const video = snap.kind === 'video';
  const remoteVideo = video && RTCView && snap.remote && snap.status === 'live' && snap.remote.getVideoTracks && snap.remote.getVideoTracks().length > 0;
  const localVideo = video && RTCView && snap.local && !snap.camOff;
  const ended = snap.status === 'ended';
  const asking = snap.status === 'incoming';
  const face = ui.s(ui.height < 640 ? 96 : 116);
  const btn = ui.tap(62);
  const big = ui.tap(70);
  const ringScale = pulse.interpolate({ inputRange: [0, 1], outputRange: [1, 1.55] });
  const ringFade = pulse.interpolate({ inputRange: [0, 1], outputRange: [0.45, 0] });

  return (
    <View style={StyleSheet.absoluteFill}>
      {remoteVideo ? (
        <RTCView streamURL={snap.remote.toURL()} style={StyleSheet.absoluteFill} objectFit="cover" zOrder={0} />
      ) : (
        <LinearGradient colors={['#2A2D35', GRAD.dark[0], GRAD.dark[1]]} start={{ x: 0.2, y: 0 }} end={{ x: 0.8, y: 1 }} style={StyleSheet.absoluteFill} />
      )}
      {remoteVideo ? <View style={[StyleSheet.absoluteFill, { backgroundColor: 'rgba(11,12,15,0.28)' }]} /> : null}

      <View style={{ paddingTop: inset.top + 14, paddingHorizontal: 20, alignItems: 'center' }}>
        <View style={styles.brand}>
          <Img src={LOGO} plain fit="contain" style={{ width: 44, height: 16 }} />
          <T v="label" size={10.5} color="#D9DCE2" style={{ marginLeft: 8, letterSpacing: 1.4 }}>{video ? 'Video call' : 'Voice call'}</T>
        </View>
      </View>

      <View style={styles.middle}>
        {!remoteVideo ? (
          <View style={{ width: face * 1.6, height: face * 1.6, alignItems: 'center', justifyContent: 'center' }}>
            {ringing ? (
              <Animated.View style={{ position: 'absolute', width: face, height: face, borderRadius: face / 2, backgroundColor: C.white, opacity: ringFade, transform: [{ scale: ringScale }] }} />
            ) : null}
            <View style={[styles.faceRing, { borderRadius: face / 2 + 5 }]}>
              <View style={{ width: face, height: face, borderRadius: face / 2, backgroundColor: FACES[Math.abs(snap.hue | 0) % FACES.length], alignItems: 'center', justifyContent: 'center' }}>
                <T v="display" size={face * 0.32} color={C.white}>{initials(snap.peer)}</T>
              </View>
            </View>
          </View>
        ) : null}
        <T v="h1" color={C.white} lines={1} center style={{ marginTop: remoteVideo ? 0 : 10, paddingHorizontal: 24 }}>{snap.peer || 'SBK'}</T>
        <T v="body" color={ended ? '#FFD2D6' : '#C9CDD5'} lines={2} center style={{ marginTop: 6, paddingHorizontal: 28 }}>{snap.text}</T>
      </View>

      {localVideo ? (
        <View style={[styles.pip, { top: inset.top + 56, right: 16, width: ui.s(104), height: ui.s(148) }]}>
          <RTCView streamURL={snap.local.toURL()} style={{ flex: 1 }} objectFit="cover" mirror={snap.front} zOrder={1} />
        </View>
      ) : null}

      {!ended ? (
        <View style={{ paddingBottom: inset.bottom + 34, paddingHorizontal: 18 }}>
          {asking ? (
            <View style={[styles.row, { justifyContent: 'space-evenly' }]}>
              <Round size={big} icon="call" rotate label="Decline" tone="end" onPress={calls.hangUp} />
              <Round size={big} icon={video ? 'videocam' : 'call'} label="Answer" tone="go" onPress={calls.answer} />
            </View>
          ) : (
            <>
              <View style={[styles.row, { justifyContent: 'center', marginBottom: 22 }]}>
                <Round size={btn} icon={snap.muted ? 'mic-off' : 'mic'} label={snap.muted ? 'Unmute' : 'Mute'} active={snap.muted} onPress={calls.toggleMute} />
                <Round size={btn} icon={snap.speaker ? 'volume-high' : 'volume-medium'} label="Speaker" active={snap.speaker} onPress={calls.toggleSpeaker} />
                {video ? <Round size={btn} icon={snap.camOff ? 'videocam-off' : 'videocam'} label={snap.camOff ? 'Camera on' : 'Camera off'} active={snap.camOff} onPress={calls.toggleCam} /> : null}
                {video ? <Round size={btn} icon="camera-reverse" label="Flip" onPress={calls.switchCamera} /> : null}
              </View>
              <View style={[styles.row, { justifyContent: 'center' }]}>
                <Round size={big} icon="call" rotate label="End" tone="end" onPress={calls.hangUp} />
              </View>
            </>
          )}
        </View>
      ) : (
        <View style={{ height: inset.bottom + 34 + big + 30 }} />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  brand: { flexDirection: 'row', alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.08)', borderRadius: 20, paddingHorizontal: 12, paddingVertical: 6 },
  middle: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  faceRing: { backgroundColor: 'rgba(255,255,255,0.12)', padding: 5 },
  row: { flexDirection: 'row', alignItems: 'flex-start' },
  pip: { position: 'absolute', overflow: 'hidden', backgroundColor: '#000', borderRadius: 14, borderWidth: 2, borderColor: 'rgba(255,255,255,0.7)' },
});
