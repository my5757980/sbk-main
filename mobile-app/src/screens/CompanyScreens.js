/* About Us, Services and Official Partners - the website's company pages,
   word for word, laid out for a phone. */
import { ScrollView, StyleSheet, View } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, R, lift, useUI } from '../theme';
import { ABOUT, PARTNERS, SERVICES } from '../api/static';
import { Button, Eyebrow, Img, T } from '../components/base';
import { PageHero, SectionHead, Wrap } from '../components/layout';
import { useGo } from '../nav';
import { whatsApp } from '../actions';

/* ------------------------------------------------------------- about us */

export function AboutScreen() {
  const ui = useUI();
  const go = useGo();
  const a = ABOUT;
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero eyebrow={a.eyebrow} title={a.title} compact />
      <Wrap style={{ paddingTop: 20 }}>
        <View style={styles.photoWrap}>
          <Img src={a.image} style={{ width: '100%', aspectRatio: 4 / 3, borderRadius: R.lg }} />
          <LinearGradient colors={GRAD.red} style={styles.experience}>
            <T v="display" size={26} color={C.white}>{a.experience[0]}</T>
            <T v="label" size={9.5} color={C.white} style={{ marginTop: 2 }}>{a.experience[1]}</T>
          </LinearGradient>
        </View>

        <Eyebrow style={{ marginTop: 24 }}>{a.whoLabel}</Eyebrow>
        <T v="h1">{a.whoTitle}</T>
        <T v="h1" color={C.red}>{a.whoTitleRed}</T>
        {a.who.map((p) => <T key={p.slice(0, 20)} v="body" color={C.ink2} style={{ marginTop: 12 }}>{p}</T>)}
        <View style={styles.signature}>
          <View style={styles.sigLine} />
          <View>
            <T v="title">{a.signature[0]}</T>
            <T v="small" color={C.muted} weight="500">{a.signature[1]}</T>
          </View>
        </View>

        <View style={[styles.card, { marginTop: 26, padding: 0, overflow: 'hidden' }]}>
          <Img src={a.ceo.image} style={{ width: '100%', aspectRatio: ui.tablet ? 2 : 1.1 }} contentPosition="top" />
          <View style={{ padding: 18 }}>
            <Eyebrow>{a.ceo.label}</Eyebrow>
            <T v="h2">{a.ceo.title}</T>
            <T v="h2" color={C.red}>{a.ceo.titleRed}</T>
            {a.ceo.text.map((p) => <T key={p.slice(0, 20)} v="body" color={C.ink2} style={{ marginTop: 10 }}>{p}</T>)}
            <T v="title" style={{ marginTop: 14 }}>{a.ceo.name[0]}</T>
            <T v="small" color={C.muted} weight="500">{a.ceo.name[1]}</T>
          </View>
        </View>

        <LinearGradient colors={GRAD.dark} style={[styles.book, { marginTop: 14 }]}>
          <T v="h3" color={C.white} style={{ flex: 1 }}>{a.bookCar}</T>
          <Button title="WhatsApp" kind="whatsapp" icon="logo-whatsapp" size="sm" onPress={() => whatsApp()} />
        </LinearGradient>

        {a.values.map((v) => (
          <View key={v.title} style={[styles.card, { marginTop: 14 }]}>
            <View style={styles.valueIcon}>
              <Ionicons name={v.icon} size={22} color={C.white} />
            </View>
            <T v="h3" style={{ marginTop: 12 }}>{v.title}</T>
            <T v="title" size={13.5} color={C.red} style={{ marginTop: 4 }}>{v.lead}</T>
            <T v="body" color={C.ink2} style={{ marginTop: 8 }}>{v.text}</T>
          </View>
        ))}

        <LinearGradient colors={GRAD.redDeep} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={[styles.cta, { marginTop: 22 }]}>
          <Eyebrow color="#FFD5D6">{a.cta.eyebrow}</Eyebrow>
          <T v="h2" color={C.white}>{a.cta.title}</T>
          <T v="body" color="rgba(255,255,255,0.88)" style={{ marginTop: 8 }}>{a.cta.text}</T>
          <Img src={a.cta.image} plain fit="contain" style={{ width: '100%', height: ui.s(190), marginTop: 10 }} />
          <Button title={a.cta.button} kind="light" iconRight="arrow-forward" onPress={() => go.contact('Vehicle sourcing')} full />
        </LinearGradient>
      </Wrap>
    </ScrollView>
  );
}

/* ------------------------------------------------------------- services */

export function ServicesScreen() {
  const ui = useUI();
  const go = useGo();
  const s = SERVICES;
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero title={s.title} text={s.introTitle} compact />
      <Wrap style={{ paddingTop: 20 }}>
        <T v="body" color={C.ink2}>{s.intro}</T>
        {s.items.map((it, i) => {
          const dark = it.dark;
          const body = (
            <>
              <Img src={it.image} style={{ width: '100%', aspectRatio: 16 / 10, borderRadius: R.md }} />
              <T v="label" color={dark ? '#FF5A60' : C.red} style={{ marginTop: 16 }}>Service {String(i + 1).padStart(2, '0')}</T>
              <T v="h2" color={dark ? C.white : C.ink} style={{ marginTop: 4 }}>{it.title}</T>
              <T v="body" color={dark ? 'rgba(255,255,255,0.8)' : C.ink2} style={{ marginTop: 8 }}>{it.text}</T>
              <Button
                title={it.button}
                kind={dark ? 'primary' : 'dark'}
                iconRight="arrow-forward"
                onPress={() => (it.button === 'View Stock' ? go.shop({}) : go.contact(it.title))}
                style={{ alignSelf: 'flex-start', marginTop: 14 }}
              />
            </>
          );
          return dark ? (
            <LinearGradient key={it.title} colors={GRAD.dark} style={[styles.service, { marginTop: 16 }]}>{body}</LinearGradient>
          ) : (
            <View key={it.title} style={[styles.service, styles.card, { marginTop: 16 }]}>{body}</View>
          );
        })}

        <LinearGradient colors={GRAD.redDeep} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={[styles.cta, { marginTop: 22 }]}>
          <T v="h2" color={C.white}>{s.cta.title}</T>
          <T v="body" color="rgba(255,255,255,0.88)" style={{ marginTop: 8 }}>{s.cta.text}</T>
          <Img src={s.cta.image} plain fit="contain" style={{ width: '100%', height: ui.s(150), marginTop: 8 }} />
          <Button title={s.cta.buttons[0]} kind="light" onPress={() => go.contact('Consultation')} full />
          <Button title={s.cta.buttons[1]} kind="ghostLight" onPress={() => go.contact()} full style={{ marginTop: 10 }} />
        </LinearGradient>
      </Wrap>
    </ScrollView>
  );
}

/* ------------------------------------------------------------- partners */

export function PartnersScreen() {
  const ui = useUI();
  const go = useGo();
  const p = PARTNERS;
  const half = ui.width >= 600 ? '32%' : '100%';
  return (
    <ScrollView style={{ flex: 1, backgroundColor: C.bg }} contentContainerStyle={{ paddingBottom: 36 }}>
      <PageHero eyebrow={p.eyebrow} title={p.title} text={p.text} compact>
        <View style={styles.proofPanel}>
          <T v="label" color="#FF5A60">{p.panelLabel}</T>
          <T v="h3" color={C.white} style={{ marginTop: 4, marginBottom: 6 }}>{p.panelTitle}</T>
          {p.proof.map(([n, title, text]) => (
            <View key={n} style={styles.proof}>
              <T v="h3" color={C.red} style={{ width: 34 }}>{n}</T>
              <View style={{ flex: 1 }}>
                <T v="title" color={C.white}>{title}</T>
                <T v="small" color="rgba(255,255,255,0.7)" weight="500">{text}</T>
              </View>
            </View>
          ))}
        </View>
      </PageHero>
      <Wrap style={{ paddingTop: 20 }}>
        <View style={styles.wrapRow}>
          {p.strip.map(([label, title, text]) => (
            <View key={label} style={[styles.card, { width: half, marginBottom: 12 }]}>
              <T v="label" color={C.red}>{label}</T>
              <T v="h3" style={{ marginTop: 4 }}>{title}</T>
              <T v="sub" color={C.muted} style={{ marginTop: 6 }}>{text}</T>
            </View>
          ))}
        </View>

        <SectionHead title={p.networkTitle} lead={p.networkText} style={{ marginTop: 18 }} />
        {p.network.map((n) => (
          <View key={n.name} style={[styles.card, { flexDirection: 'row', alignItems: 'center', marginBottom: 12 }]}>
            <View style={styles.partnerLogo}>
              <Img src={n.image} plain fit="contain" style={{ width: '100%', height: '100%' }} />
            </View>
            <View style={{ flex: 1, marginLeft: 14 }}>
              <T v="label" color={C.red}>{n.type}</T>
              <T v="h3" style={{ marginTop: 2 }}>{n.name}</T>
              <T v="sub" color={C.muted} style={{ marginTop: 4 }}>{n.summary}</T>
            </View>
          </View>
        ))}

        <SectionHead title={p.globalTitle} lead={p.globalText} style={{ marginTop: 18 }} />
        <View style={styles.wrapRow}>
          {p.global.map((g) => (
            <View key={g.name} style={[styles.card, { width: ui.width >= 600 ? '32%' : '48.5%', alignItems: 'center', marginBottom: 12 }]}>
              <Img src={g.image} plain fit="contain" style={{ width: '100%', height: 70 }} />
              <T v="label" size={10} color={C.muted} center style={{ marginTop: 10 }}>{g.type}</T>
            </View>
          ))}
        </View>

        <LinearGradient colors={GRAD.dark} style={[styles.cta, { marginTop: 18 }]}>
          <T v="h2" color={C.white}>{p.ctaTitle}</T>
          <T v="body" color="rgba(255,255,255,0.8)" style={{ marginTop: 8 }}>{p.ctaText}</T>
          <Button title={p.ctaButton} onPress={() => go.contact('Partner verification')} full style={{ marginTop: 16 }} />
        </LinearGradient>
      </Wrap>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: C.card, borderRadius: R.lg, padding: 16, ...lift },
  photoWrap: { position: 'relative' },
  experience: { position: 'absolute', right: 12, bottom: -16, borderRadius: R.md, paddingHorizontal: 14, paddingVertical: 10, alignItems: 'center' },
  signature: { flexDirection: 'row', alignItems: 'center', marginTop: 18 },
  sigLine: { width: 36, height: 3, backgroundColor: C.red, marginRight: 12, borderRadius: 2 },
  book: { flexDirection: 'row', alignItems: 'center', borderRadius: R.lg, padding: 16 },
  valueIcon: { width: 46, height: 46, borderRadius: 12, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center' },
  cta: { borderRadius: R.xl, padding: 20, overflow: 'hidden' },
  service: { borderRadius: R.lg, padding: 14 },
  proofPanel: { marginTop: 20, borderRadius: R.lg, padding: 16, backgroundColor: 'rgba(255,255,255,0.06)', borderWidth: 1, borderColor: 'rgba(255,255,255,0.1)' },
  proof: { flexDirection: 'row', alignItems: 'flex-start', marginTop: 12 },
  wrapRow: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  partnerLogo: { width: 92, height: 64, borderRadius: R.md, backgroundColor: C.bg, padding: 8 },
});
