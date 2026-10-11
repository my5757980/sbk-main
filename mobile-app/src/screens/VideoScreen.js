/* A client video from the website's home page, played full screen. */
import { StyleSheet, View } from 'react-native';
import { useNavigation, useRoute } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { useVideoPlayer, VideoView } from 'expo-video';

import { IconButton } from '../components/base';
import { siteUrl } from '../config';

export default function VideoScreen() {
  const nav = useNavigation();
  const insets = useSafeAreaInsets();
  const { path } = useRoute().params || {};
  const player = useVideoPlayer(siteUrl(path), (p) => {
    p.loop = false;
    p.play();
  });

  return (
    <View style={styles.root}>
      <StatusBar style="light" />
      <VideoView player={player} style={StyleSheet.absoluteFill} contentFit="contain" nativeControls fullscreenOptions={{ enable: true }} allowsPictureInPicture={false} />
      <View style={[styles.close, { top: insets.top + 8 }]}>
        <IconButton name="close" kind="dark" onPress={() => nav.goBack()} label="Close video" />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  root: { flex: 1, backgroundColor: '#000' },
  close: { position: 'absolute', left: 12 },
});
