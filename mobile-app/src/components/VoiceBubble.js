/* A voice note in the chat, playable in place - the SBK Chat app's player,
   which was proven on real phones.

   One player per bubble, empty until pressed (a long conversation downloads no
   voice note until one is played), and playing one stops any other. At the
   end it pauses and goes back to the start: on Android the player underneath
   reaches the end still set to "play", and seeking back without the pause
   started it again, for ever (the SBK Chat app, 19 September 2026). */
import { useEffect, useRef, useState } from 'react';
import { StyleSheet, View } from 'react-native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C } from '../theme';
import { audioLib } from '../lazy';
import { Press, T } from './base';

const playing = new Set();

function stopOthers(except) {
  Array.from(playing).forEach((stop) => { if (stop !== except) stop(); });
}

function fmt(sec) {
  const s = Math.max(0, Math.round(sec || 0));
  return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
}

export default function VoiceBubble({ source, mine, knownSecs }) {
  const A = audioLib();
  const player = A ? A.useAudioPlayer() : null;
  const st = A ? A.useAudioPlayerStatus(player) : null;
  const [loaded, setLoaded] = useState(false);
  const stopRef = useRef(null);

  if (!stopRef.current) {
    stopRef.current = () => {
      try { if (player) player.pause(); } catch (e) { /* already gone */ }
      playing.delete(stopRef.current);
    };
  }

  useEffect(() => () => { playing.delete(stopRef.current); }, []);

  useEffect(() => {
    if (player && st && st.didJustFinish) {
      playing.delete(stopRef.current);
      try { player.pause(); } catch (e) { /* */ }
      try { player.seekTo(0); } catch (e) { /* */ }
    }
  }, [st && st.didJustFinish, player]); // eslint-disable-line react-hooks/exhaustive-deps

  async function toggle() {
    if (!A || !player) return;
    try {
      if (st && st.playing) { stopRef.current(); return; }
      stopOthers(stopRef.current);
      if (!loaded) {
        try { await A.setAudioModeAsync({ playsInSilentMode: true, allowsRecording: false }); } catch (e) { /* */ }
        player.replace(source);
        setLoaded(true);
      } else if (st && st.duration > 0 && st.currentTime >= st.duration - 0.05) {
        await player.seekTo(0);
      }
      player.play();
      playing.add(stopRef.current);
    } catch (e) { /* the bubble just stays still */ }
  }

  const isPlaying = !!(st && st.playing);
  const waiting = loaded && !isPlaying && !!(st && st.isBuffering);
  const dur = st && st.duration > 0 ? st.duration : (knownSecs || 0);
  const pos = (st && st.currentTime) || 0;
  const ink = mine ? C.white : C.ink;
  const frac = dur > 0 ? Math.min(1, pos / dur) : 0;

  return (
    <Press onPress={toggle} style={styles.row} accessibilityLabel={isPlaying ? 'Pause voice note' : 'Play voice note'}>
      <View style={[styles.button, { backgroundColor: mine ? 'rgba(255,255,255,0.2)' : C.redWash }]}>
        <Ionicons name={waiting ? 'ellipsis-horizontal' : isPlaying ? 'pause' : 'play'} size={18} color={mine ? C.white : C.red} />
      </View>
      <View style={{ flex: 1, marginLeft: 10 }}>
        <View style={[styles.track, { backgroundColor: mine ? 'rgba(255,255,255,0.35)' : C.line }]}>
          <View style={[styles.fill, { width: frac * 100 + '%', backgroundColor: ink }]} />
        </View>
        <T v="small" size={11} weight="600" color={ink} style={{ marginTop: 5, opacity: 0.85 }}>
          {A ? fmt(isPlaying || pos > 0 ? pos : dur) : 'Voice note'}
        </T>
      </View>
    </Press>
  );
}

const styles = StyleSheet.create({
  row: { flexDirection: 'row', alignItems: 'center', minWidth: 170, paddingVertical: 2 },
  button: { width: 38, height: 38, borderRadius: 19, alignItems: 'center', justifyContent: 'center' },
  track: { height: 3, borderRadius: 2, overflow: 'hidden' },
  fill: { height: 3, borderRadius: 2 },
});
