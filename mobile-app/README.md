# SBK Global Auto Trading - the app

Sir's instruction (11 Oct 2026):
- The app holds the website's **frontend**, the customer side: the home page, the shop, every page a customer reads, the account, and the live chat.
- The admin dashboard, the auction and the CRM stay on the website and are **not** in the app.
- There is **one database**, the website's. The UI/UX comes first; the data is then connected to the website's database.

It is a native app (React Native / Expo), not a web page in a frame. Spec, plan and tasks: `specs/005-sbk-app/`.

| | |
|---|---|
| Android package | `com.sbkautotrading.app` (version 2.0.0, code 2). It updates the earlier test build, and installs beside SBK Chat (`com.sbkautotrading.chat`). |
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
src/components/         the shared pieces every screen is built from
src/screens/            one file per screen or group of screens
tools/build-content.py  copies the website's public content into content.json
tools/build-assets.py   makes phone-sized copies of the website's fixed pictures
```

## Phase 1 and phase 2

- **Phase 1 (now):** every screen, with the website's real content. The cart and wishlist work. Forms check themselves like the website's. Sending (orders, messages, login, chat) is **not** connected yet, and each screen says so in plain words.
- **Phase 2:** the website gets a JSON API on its own database (`/api/app/*`, token login for customers). `src/api/index.js` switches to it, and the chat connects to the website's chat. No screen changes.

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
npx expo prebuild --platform android
# android/local.properties:  sdk.dir=C:/Users/ASFAR/AppData/Local/Android/Sdk
cd android
set JAVA_HOME=C:\Program Files\Android\Android Studio\jbr
gradlew :app:assembleRelease --no-daemon --max-workers=1 --console=plain -x lintVitalRelease -x lintVitalReportRelease -x lintVitalAnalyzeRelease
```

The APK lands in `android/app/build/outputs/apk/release/app-release.apk`. `plugins/withLowMemoryBuild.js` keeps the build within this 4 GB machine.
