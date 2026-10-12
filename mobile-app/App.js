/* SBK Global Auto Trading - the website's frontend, as a real app.
   ---------------------------------------------------------------------------
   Sir's instruction (11 Oct 2026): the app holds the website's customer side -
   the home page, the shop, every page a customer reads, the account and the
   live chat - and NOT the admin dashboard, the auction or the CRM, which stay on
   the website. One database: the website's. The UI/UX comes first; the data is
   then connected to the website's database (src/api is the only door to it).

   Five tabs - Home, Shop, Chat, Cart, Menu - and every other page on a native
   stack above them, so Back always does what a phone user expects. */
import { Component, useEffect } from 'react';
import { StyleSheet, View } from 'react-native';
import { NavigationContainer, DefaultTheme, createNavigationContainerRef } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { SafeAreaProvider, useSafeAreaInsets } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C } from './src/theme';
import { Button, T } from './src/components/base';
import { ShopProvider, useShop } from './src/store/shop';
import { AccountProvider } from './src/store/account';
import { ChatProvider, useChat } from './src/store/chat';
import { FiltersProvider } from './src/store/filters';

import HomeScreen from './src/screens/HomeScreen';
import ShopScreen from './src/screens/ShopScreen';
import FiltersScreen from './src/screens/FiltersScreen';
import ProductScreen from './src/screens/ProductScreen';
import GalleryScreen from './src/screens/GalleryScreen';
import VideoScreen from './src/screens/VideoScreen';
import CartScreen from './src/screens/CartScreen';
import WishlistScreen from './src/screens/WishlistScreen';
import CheckoutScreen from './src/screens/CheckoutScreen';
import MenuScreen from './src/screens/MenuScreen';
import ProfileScreen from './src/screens/ProfileScreen';
import { ForgotScreen, LoginScreen, RegisterScreen } from './src/screens/AuthScreens';
import { AboutScreen, PartnersScreen, ServicesScreen } from './src/screens/CompanyScreens';
import { BankScreen, ContactScreen, FaqScreen, LocationsScreen } from './src/screens/ContactScreens';
import { BlogPostScreen, BlogScreen } from './src/screens/BlogScreens';
import { ChatScreen, ChatThreadScreen } from './src/screens/ChatScreens';
import CallScreen from './src/screens/CallScreen';
import { CallProvider } from './src/calls';
import ChatBeat from './src/calls/beat';
import { onTap, startPush, stopPush } from './src/push';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const navTheme = {
  ...DefaultTheme,
  colors: { ...DefaultTheme.colors, primary: C.red, background: C.bg, card: C.card, text: C.ink, border: C.line, notification: C.red },
};

const TAB_ICONS = {
  Home: ['home', 'home-outline'],
  Shop: ['car-sport', 'car-sport-outline'],
  Chat: ['chatbubbles', 'chatbubbles-outline'],
  Cart: ['cart', 'cart-outline'],
  Menu: ['grid', 'grid-outline'],
};

function Tabs() {
  const shop = useShop();
  const chat = useChat();
  const insets = useSafeAreaInsets();
  return (
    <Tab.Navigator
      backBehavior="firstRoute"
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: C.red,
        tabBarInactiveTintColor: '#8A9099',
        tabBarHideOnKeyboard: true,
        // React Navigation's 49pt bar cut the bold 11pt labels: a little taller,
        // and the bottom inset (gesture bar) is still added by the library itself.
        tabBarStyle: { backgroundColor: C.card, borderTopColor: C.line, height: 58 + insets.bottom },
        tabBarLabelStyle: { fontSize: 11, lineHeight: 14, fontWeight: '700' },
        tabBarAllowFontScaling: false,
        tabBarIcon: ({ focused, color }) => (
          <Ionicons name={TAB_ICONS[route.name][focused ? 0 : 1]} size={23} color={color} />
        ),
        tabBarBadgeStyle: { backgroundColor: C.red, color: C.white, fontSize: 10, fontWeight: '800' },
      })}
    >
      <Tab.Screen name="Home" component={HomeScreen} />
      <Tab.Screen name="Shop" component={ShopScreen} />
      <Tab.Screen name="Chat" component={ChatScreen} options={{ tabBarBadge: chat.unread || undefined }} />
      <Tab.Screen name="Cart" component={CartScreen} options={{ tabBarBadge: shop.cart.length || undefined }} />
      <Tab.Screen name="Menu" component={MenuScreen} />
    </Tab.Navigator>
  );
}

const navRef = createNavigationContainerRef();

/* Notifications follow whoever is in the chat; a tapped one opens its
   conversation (or the chat, for a call). */
function Push({ who }) {
  useEffect(() => {
    if (!who) return undefined;
    startPush();
    return () => { stopPush(); };
  }, [who]);
  useEffect(() => onTap((b) => {
    const go = () => {
      if (b.type === 'message' && b.to) navRef.navigate('ChatThread', { staffId: b.to, threadId: b.thread || null, name: b.name || 'SBK' });
      else navRef.navigate('Tabs', { screen: 'Chat' });
    };
    if (navRef.isReady()) go();
    else setTimeout(go, 800);
  }), []);
  return null;
}

/* Calls belong to whoever is in the chat: the call screen comes up over any
   page, and the chat's beat notices a ring wherever the customer is. */
function WithCalls({ children }) {
  const chat = useChat();
  const w = chat.who;
  const key = w && !w.staff && !w.details ? 'chat:' + ((w.me && w.me.email) || 'me') : null;
  return (
    <CallProvider who={key}>
      {children}
      <ChatBeat />
      <Push who={key} />
      <CallScreen />
    </CallProvider>
  );
}

/* A page that fails to draw shows a way back instead of closing the app. */
class Guard extends Component {
  constructor(props) {
    super(props);
    this.state = { failed: false };
  }

  static getDerivedStateFromError() {
    return { failed: true };
  }

  render() {
    if (!this.state.failed) return this.props.children;
    return (
      <View style={styles.failed}>
        <Ionicons name="warning-outline" size={40} color={C.red} />
        <T v="h2" center style={{ marginTop: 12 }}>Something went wrong</T>
        <T v="sub" color={C.muted} center style={{ marginTop: 6 }}>Please try again.</T>
        <Button title="Try again" onPress={() => this.setState({ failed: false })} style={{ marginTop: 18, minWidth: 180 }} />
      </View>
    );
  }
}

export default function App() {
  return (
    <SafeAreaProvider>
      <Guard>
        <AccountProvider>
          <ShopProvider>
            <ChatProvider>
              <FiltersProvider>
                <WithCalls>
                <NavigationContainer ref={navRef} theme={navTheme}>
                  <StatusBar style="dark" />
                  <Stack.Navigator
                    screenOptions={{
                      headerTintColor: C.ink,
                      headerTitleStyle: { fontWeight: '800', fontSize: 17 },
                      headerShadowVisible: false,
                      headerStyle: { backgroundColor: C.card },
                      headerBackButtonDisplayMode: 'minimal',
                      contentStyle: { backgroundColor: C.bg },
                      animation: 'slide_from_right',
                    }}
                  >
                    <Stack.Screen name="Tabs" component={Tabs} options={{ headerShown: false }} />
                    <Stack.Screen name="Product" component={ProductScreen} options={{ headerShown: false }} />
                    <Stack.Screen name="Gallery" component={GalleryScreen} options={{ headerShown: false, animation: 'fade', contentStyle: { backgroundColor: '#000' } }} />
                    <Stack.Screen name="Video" component={VideoScreen} options={{ headerShown: false, animation: 'fade', contentStyle: { backgroundColor: '#000' } }} />
                    <Stack.Screen name="Filters" component={FiltersScreen} options={{ title: 'Filters', animation: 'slide_from_bottom' }} />
                    <Stack.Screen name="Wishlist" component={WishlistScreen} options={{ title: 'Your Wishlist' }} />
                    <Stack.Screen name="Checkout" component={CheckoutScreen} options={{ title: 'Checkout' }} />
                    <Stack.Screen name="Login" component={LoginScreen} options={{ title: 'Login' }} />
                    <Stack.Screen name="Register" component={RegisterScreen} options={{ title: 'Create Account' }} />
                    <Stack.Screen name="Forgot" component={ForgotScreen} options={{ title: 'Forgot Password' }} />
                    <Stack.Screen name="Profile" component={ProfileScreen} options={{ title: 'My Profile' }} />
                    <Stack.Screen name="About" component={AboutScreen} options={{ title: 'About SBK' }} />
                    <Stack.Screen name="Services" component={ServicesScreen} options={{ title: 'Services' }} />
                    <Stack.Screen name="Partners" component={PartnersScreen} options={{ title: 'Official Partners' }} />
                    <Stack.Screen name="Locations" component={LocationsScreen} options={{ title: 'Our Locations' }} />
                    <Stack.Screen name="Bank" component={BankScreen} options={{ title: 'Bank Details' }} />
                    <Stack.Screen name="Contact" component={ContactScreen} options={{ title: 'Contact Us' }} />
                    <Stack.Screen name="Faq" component={FaqScreen} options={{ title: 'FAQ' }} />
                    <Stack.Screen name="Blog" component={BlogScreen} options={{ title: 'Blogs' }} />
                    <Stack.Screen name="BlogPost" component={BlogPostScreen} options={{ title: '' }} />
                    <Stack.Screen name="ChatThread" component={ChatThreadScreen} options={{ title: '' }} />
                  </Stack.Navigator>
                </NavigationContainer>
                </WithCalls>
              </FiltersProvider>
            </ChatProvider>
          </ShopProvider>
        </AccountProvider>
      </Guard>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  failed: { flex: 1, alignItems: 'center', justifyContent: 'center', padding: 24, backgroundColor: C.bg },
});
