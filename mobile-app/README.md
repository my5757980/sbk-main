# SBK Global Auto Trading - the app

Sir's instruction (11 Oct 2026):
- The app holds the website's **frontend**, the customer side: the home page, the shop, every page a customer reads, the account, and the live chat.
- The admin dashboard, the auction and the CRM stay on the website and are **not** in the app.
- There is **one database**, the website's. The UI/UX comes first; the data is then connected to the website's database.

It is a native app (React Native / Expo), not a web page in a frame. Spec, plan and tasks: `specs/005-sbk-app/`.

| | |
|---|---|
| Android package | `com.sbkautotrading.app` (version 2.1.0, code 3). It updates the earlier builds, and installs beside SBK Chat (`com.sbkautotrading.chat`). |
| Name / icon | "SBK Global Auto Trading", with the SBK mark; the splash shows the SBK logo on black |
| Tabs | Home · Shop · Chat · Cart · Menu |

## Where things are

```
App.js                  navigation: five tabs, and every other screen on a stack
src/theme.js            the website's colours and type; one scale for every phone
src/api/index.js        THE ONLY DOOR TO DATA - every screen asks this file
src/api/content.json    phase 1: the website's vehicles, blogs, filter lists (generated)
src/api/static.js       the website's fixed texts, word for word from its views
src/store/              cart, wishlist, account, chat, shop filters (kept on the phone)
src/calls/              voice and video calls with the desk (the SBK Chat app's call engine)
src/push.js             notifications: the desk's replies and calls when the app is closed
src/components/         the shared pieces every screen is built from
src/screens/            one file per screen or group of screens
tools/build-content.py  copies the website's public content into content.json
tools/build-assets.py   makes phone-sized copies of the website's fixed pictures
```

## The website's database

- The app reads and writes the website's own database through the website's API for the app (`routes/app.php`, `/api/app/*`): vehicles, blogs, accounts (one account with the website), orders ("Submit Order for Checking"), the contact form, and the live chat (the same conversations the desk answers).
- `src/api/index.js` is the only door to data. When the website cannot be reached (no connection, or a website without the API), the reading screens show the copy the app carries (`src/api/bundled.js`, `content.json`); signing in, orders and the chat need the website.
- **Voice and video calls** with the desk, both ways, on the website chat's own calls (`/api/app/chat/call`; WebRTC, the SBK Chat app's engine). Ringing in the app is heard while the app is open; when it is closed, a notification rings.
- **PayPal / card**, as the website's checkout: "Pay $X Now" opens the website's payment page (`/app/pay/{key}`) in the phone's browser and comes back to the app when PayPal has taken the payment. The order is written like the website's.
- **Notifications** (Firebase project `sbk-chat`, `google-services.json`): the desk's replies, ringing and missed calls, in two channels the customer can switch off separately (Messages, Calls). Tapping one opens the conversation.

What the live website needs for these:
- `php artisan migrate` (table `sbk_app_push`, the phones that get notifications).
- `.env` `FCM_SERVICE_ACCOUNT=<path of the sbk-chat service-account key, outside the web root>`. Without it nothing is sent and everything else works.
- PayPal's `PAYPAL_*` settings, the same ones the website's checkout uses.

To refresh the bundled content from the live website:

```
python tools/build-content.py https://v2.sbkautotrading.com
```

## The website's address

Pictures and, in phase 2, all data come from `EXPO_PUBLIC_SITE_URL`, set when the app is built. The default is `https://v2.sbkautotrading.com`, where the website runs today. Build with the final domain once the website moves.

## Building the APK (this machine: Node 24, JDK 21, Android SDK)

Run everything from this `mobile-app/` folder. The website around it is not involved in the build.

```
npm install
npx expo prebuild --platform android --clean
# android/local.properties:  sdk.dir=C:/Users/ASFAR/AppData/Local/Android/Sdk
cd android
set JAVA_HOME=C:\Program Files\Android\Android Studio\jbr
gradlew :app:assembleRelease --no-daemon --max-workers=1 --console=plain -x lintVitalRelease -x lintVitalReportRelease -x lintVitalAnalyzeRelease
```

The APK lands in `android/app/build/outputs/apk/release/app-release.apk`. `plugins/withLowMemoryBuild.js` keeps the build within this 4 GB machine.
