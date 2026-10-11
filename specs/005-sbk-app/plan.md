# Plan: the SBK app (phase 1, UI/UX)

## Stack
- **Expo SDK 57 / React Native 0.86 / React 19.2.** These are the versions of the earlier app, whose installed modules are reused here.
- **Navigation:** React Navigation 7 (native stack + bottom tabs) on react-native-screens. Transitions and back gestures are native.
- **Look:**
  - expo-image for fast, cached pictures;
  - expo-linear-gradient for the website's red and black panels;
  - Ionicons from @expo/vector-icons.
- **Storage:** AsyncStorage keeps the cart and wishlist on the phone. SecureStore will keep the phase-2 login token.
- **Package:** `com.sbkautotrading.app`, the SBK icons, the label "SBK Global Auto Trading". The new app installs over the earlier WebView app.

## Code layout (`mobile-app/`)
```
App.js                    navigation container + providers
src/theme.js              colours, spacing, type scale, shadows (website's look)
src/api/                  the ONLY door to data
  index.js                getHome, getProducts(filters), getProduct(slug), getBlogs, ...
  content.js              phase 1: the website's public content (generated, see tools/)
src/store/                cart + wishlist (context + AsyncStorage), auth (phase 2)
src/components/           ProductCard, SectionHeader, Button, Chip, Field, Sheet, Empty, Skeleton...
src/screens/              one file per screen
tools/build-content.py    pulls the website's public content into src/api/content.js
```

## Data
- In phase 1, `content.js` is generated from the website's own public pages: vehicles, brands, blogs, plus the fixed texts of About, Services, Partners, Locations, Bank and FAQ.
- Its shapes are the website's models: Product, VehicleBrand, Blog and so on.
- Pictures are the website's own URLs.
- In phase 2, `src/api/index.js` fetches `/api/app/*` from the website instead. The screens do not change.

## Screens
- **Tabs:** Home, Shop, Chat, Cart, Menu.
- **Stack:**
  - Product, Gallery, Filters (sheet), Wishlist, Checkout;
  - Login, Register, Forgot, Profile, EditProfile, ChangePassword, Orders;
  - About, Services, Partners, Blog, BlogPost, Locations, Bank, Contact, FAQ;
  - ChatThread.

## Checks
- Render every screen at 320, 390 and 430 wide, plus a tablet: nothing cut off, nothing overlapping.
- Build a release APK and install it.
