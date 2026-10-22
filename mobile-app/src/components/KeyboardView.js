/* Keeps the input above the keyboard - a drop-in for KeyboardAvoidingView.

   The app draws edge-to-edge (Android 15+, targetSdk 36), so the window is NOT
   resized for the keyboard and KeyboardAvoidingView's "height" never lifts the
   box on our pages. Here we listen for the keyboard ourselves and lift the page
   by the keyboard's real reach from the bottom of the screen.

   React Native reports that height WITHOUT the navigation bar
   (ReactRootView: height = ime - systemBars), but the keyboard covers that bar
   too - lifting by the reported height alone (2.1.4, 2.1.5) left the box a
   navigation bar's depth under the keyboard: a sliver with gestures, most of
   the box with the three buttons. So the lift is the height PLUS the bar. The
   tab bar hides while typing (tabBarHideOnKeyboard), so every page that uses
   this reaches the bottom of the screen and the sum is exact.

   iOS keeps the native KeyboardAvoidingView, which has always worked there.
   Same props as KeyboardAvoidingView, so swapping the tag is the only change. */
import { useEffect, useState } from 'react';
import { Keyboard, KeyboardAvoidingView, Platform, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

export function KeyboardView({ style, children, keyboardVerticalOffset = 0, behavior, ...rest }) {
  const insets = useSafeAreaInsets();
  const [kb, setKb] = useState(0);

  useEffect(() => {
    if (Platform.OS !== 'android') return undefined;
    const show = Keyboard.addListener('keyboardDidShow', (e) => setKb((e && e.endCoordinates && e.endCoordinates.height) || 0));
    const hide = Keyboard.addListener('keyboardDidHide', () => setKb(0));
    return () => { show.remove(); hide.remove(); };
  }, []);

  if (Platform.OS === 'ios') {
    return (
      <KeyboardAvoidingView style={style} behavior={behavior || 'padding'} keyboardVerticalOffset={keyboardVerticalOffset} {...rest}>
        {children}
      </KeyboardAvoidingView>
    );
  }

  // Android (edge-to-edge): the keyboard's height plus the navigation bar it covers.
  return (
    <View style={[style, { paddingBottom: kb > 0 ? kb + insets.bottom : 0 }]} {...rest}>
      {children}
    </View>
  );
}
