/* Home - the website's home page, section by section, shaped for a phone. */
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { FlatList, ScrollView, StyleSheet, View } from 'react-native';
import { useIsFocused } from '@react-navigation/native';
import { LinearGradient } from 'expo-linear-gradient';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, GRAD, R, lift, useUI } from '../theme';
import * as api from '../api';
import { BRAND_LOGOS, FAQ, FOOTER_TEXT, HOME, LOGO, MILEAGES, PRICE_RANGES, TRANSMISSIONS } from '../api/static';
import { Button, Eyebrow, IconButton, Img, Pill, Press, T } from '../components/base';
import { Card, Page, SectionHead, TabHeader, Wrap } from '../components/layout';
import { Select } from '../components/form';
import ProductCard from '../components/ProductCard';
import { useShop } from '../store/shop';
import { useAccount } from '../store/account';
import { useGo } from '../nav';
import { CONTACT } from '../config';
import { callNumber, emailTo, whatsApp } from '../actions';

/* Two names for one maker in the stock: one tile, both names in its filter. */
const SAME_BRAND = { Mercedes: 'Mercedes-Benz' };

export default function HomeScreen() {
  const ui = useUI();
  const go = useGo();
  const shop = useShop();
  const [home, setHome] = useState(null);
  const [options, setOptions] = useState(null);
  const [refreshing, setRefreshing] = useState(false);

  const load = useCallback(async () => {
    const [h, o] = await Promise.all([api.getHome(), api.getFilterOptions()]);
    setHome(h);
    setOptions(o);
  }, []);

  useEffect(() => { load(); }, [load]);

  const refresh = async () => {
    setRefreshing(true);
    await load();
    setRefreshing(false);
  };

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader
        logo
        right={(
          <>
            <IconButton name="search-outline" onPress={() => go.shop()} label="Search vehicles" />
            <IconButton name="heart-outline" onPress={() => go.go('Wishlist')} badge={shop.wish.length} label="Wishlist" />
          </>
        )}
      />
      <Page refreshing={refreshing} onRefresh={refresh} bottom={0}>
        <Hero />
        <SearchPanel options={options} />
        <Brands brands={home ? home.brands : []} />
        <Stats />
        <Rail
          title={HOME.bestSeller.title}
          lead={HOME.bestSeller.lead}
          items={home ? home.bestSeller : null}
          tint
        />
        <WhyChoose />
        <Members />
        <Rail title={HOME.upcoming.title} lead={HOME.upcoming.lead} items={home ? home.upcoming : null} />
        <Rail title={HOME.arrivals.title} lead={HOME.arrivals.lead} items={home ? home.newArrivals : null} tint />
        <Steps />
        <Auction />
        <BodyTypes />
        <Videos />
        <Testimonials />
        <Register />
        <Guides blogs={home ? home.blogs : []} />
        <Faq />
        <Help />
        <Footer />
      </Page>
    </View>
  );
}

/* ------------------------------------------------------------------ hero */

function Hero() {
  const ui = useUI();
  const go = useGo();
  const focused = useIsFocused();
  const list = useRef(null);
  const [index, setIndex] = useState(0);
  const [held, setHeld] = useState(false);
  const h = ui.tablet ? 460 : Math.round(Math.max(400, Math.min(520, ui.width * 1.12)));

  useEffect(() => {
    if (!focused || held) return undefined;
    const t = setInterval(() => {
      setIndex((i) => {
        const next = (i + 1) % HOME.hero.length;
        list.current?.scrollToOffset({ offset: next * ui.width, animated: true });
        return next;
      });
    }, 5500);
    return () => clearInterval(t);
  }, [focused, held, ui.width]);

  return (
    <View style={{ height: h }}>
      <FlatList
        ref={list}
        data={HOME.hero}
        horizontal
        pagingEnabled
        showsHorizontalScrollIndicator={false}
        keyExtractor={(s) => s.title}
        getItemLayout={(_, i) => ({ length: ui.width, offset: ui.width * i, index: i })}
        onScrollBeginDrag={() => setHeld(true)}
        onMomentumScrollEnd={(e) => {
          setIndex(Math.round(e.nativeEvent.contentOffset.x / ui.width));
          setHeld(false);
        }}
        renderItem={({ item }) => (
          <View style={{ width: ui.width, height: h }}>
            <Img src={item.image} style={StyleSheet.absoluteFill} priority="high" />
            <LinearGradient
              colors={['rgba(11,12,15,0.86)', 'rgba(11,12,15,0.55)', 'rgba(11,12,15,0.15)']}
              start={{ x: 0, y: 0.5 }}
              end={{ x: 1, y: 0.5 }}
              style={StyleSheet.absoluteFill}
            />
            <LinearGradient colors={['transparent', 'rgba(11,12,15,0.75)']} style={[StyleSheet.absoluteFill, { top: '45%' }]} />
            <Wrap style={{ flex: 1, justifyContent: 'flex-end', paddingBottom: 64 }}>
              <Pill text="Featured" style={{ marginBottom: 12 }} />
              <T v="title" color={C.white} weight="800" style={{ letterSpacing: 0.5 }}>{item.eyebrow}</T>
              <T v="display" color={C.white} size={ui.width < 360 ? 30 : 36} style={{ marginTop: 4 }}>{item.title}</T>
              <T v="body" color="rgba(255,255,255,0.86)" weight="600" style={{ marginTop: 8, maxWidth: 520 }}>{item.subtitle}</T>
              <View style={styles.heroSpecs}>
                {item.specs.map((s) => (
                  <View key={s} style={styles.heroSpec}>
                    <T v="small" color={C.white} weight="700">{s}</T>
                  </View>
                ))}
              </View>
              <View style={styles.heroButtons}>
                <Button title="Buy Now" onPress={() => go.chat('Hi SBK, I would like to buy the ' + item.title + '.')} style={{ flex: 1 }} />
                <Button title="View Details" kind="ghostLight" onPress={() => go.shop({ search: item.search })} style={{ flex: 1, marginLeft: 10 }} />
              </View>
            </Wrap>
          </View>
        )}
      />
      <View style={[styles.stamp, { top: 16, right: ui.gutter }]} pointerEvents="none">
        <Ionicons name="boat-outline" size={16} color={C.white} />
        <T v="label" size={10} color={C.white} center style={{ marginTop: 2 }}>Ready</T>
        <T v="label" size={8.5} color={C.white} weight="700" center>to ship</T>
      </View>
      <View style={styles.dots} pointerEvents="none">
        {HOME.hero.map((s, i) => (
          <View key={s.title} style={[styles.dot, i === index && styles.dotOn]} />
        ))}
      </View>
    </View>
  );
}

/* ------------------------------------------------------- the stock search */

function SearchPanel({ options }) {
  const ui = useUI();
  const go = useGo();
  const [brand, setBrand] = useState(null);
  const [model, setModel] = useState(null);
  const [price, setPrice] = useState(null);
  const [year, setYear] = useState(null);
  const [mileage, setMileage] = useState(null);
  const [transmission, setTransmission] = useState(null);

  const brands = useMemo(() => (options ? options.brands.map((b) => ({ label: b, value: b })) : []), [options]);
  const models = useMemo(() => {
    if (!options) return [];
    return options.models
      .filter((m) => !brand || m.brand === brand)
      .map((m) => ({ label: brand ? m.name : m.brand + ' - ' + m.name, value: m.name }));
  }, [options, brand]);
  const years = useMemo(() => (options ? options.years.map((y) => ({ label: String(y), value: y })) : []), [options]);
  const prices = PRICE_RANGES.map((p, i) => ({ label: p.label, value: i }));

  const search = () => {
    const range = price != null ? PRICE_RANGES[price] : null;
    go.shop({
      brands: brand ? [brand] : [],
      model,
      year,
      maxMileage: mileage,
      transmission,
      minPrice: range ? String(range.min) : '',
      maxPrice: range && range.max ? String(range.max) : '',
    });
  };

  const two = ui.width >= 360;
  const cell = { width: two ? '48.5%' : '100%' };

  return (
    <Wrap style={{ marginTop: -30 }}>
      <View style={[styles.searchCard, ui.tablet && { padding: 24 }]}>
        <LinearGradient colors={[C.red, C.ink]} start={{ x: 0, y: 0 }} end={{ x: 1, y: 0 }} style={styles.searchLine} />
        <Eyebrow>{HOME.search.eyebrow}</Eyebrow>
        <T v="h2">{HOME.search.title}</T>
        <T v="sub" color={C.muted} style={{ marginTop: 6, marginBottom: 16 }}>{HOME.search.text}</T>
        <View style={styles.grid}>
          <Select label="Brand" value={brand} anyLabel="All Brands" options={brands} onChange={(v) => { setBrand(v); setModel(null); }} style={cell} />
          <Select label="Model" value={model} anyLabel="All Models" options={models} onChange={setModel} searchable style={cell} />
          <Select label="Price" value={price} anyLabel="Any Price" options={prices} onChange={setPrice} style={cell} />
          <Select label="Year" value={year} anyLabel="All Years" options={years} onChange={setYear} style={cell} />
          <Select label="Mileage" value={mileage} anyLabel="Any Mileage" options={MILEAGES} onChange={setMileage} style={cell} />
          <Select label="Transmission" value={transmission} anyLabel="Any Transmission" options={TRANSMISSIONS} onChange={setTransmission} style={cell} />
        </View>
        <Button title={HOME.search.button} icon="search" onPress={search} full />
        <View style={styles.quick}>
          {HOME.search.quick.map((q) => (
            <Press key={q.label} onPress={() => go.shop({ sort: q.sort })} style={styles.quickChip}>
              <T v="small" weight="800" color={C.ink2}>{q.label}</T>
            </Press>
          ))}
        </View>
      </View>
    </Wrap>
  );
}

/* ----------------------------------------------------------------- brands */

function Brands({ brands }) {
  const ui = useUI();
  const go = useGo();
  const tiles = useMemo(() => {
    const merged = new Map();
    brands.forEach((b) => {
      const name = SAME_BRAND[b.name] || b.name;
      const t = merged.get(name) || { name, count: 0, names: [] };
      t.count += b.count;
      t.names.push(b.name);
      merged.set(name, t);
    });
    return [...merged.values()].sort((a, b) => b.count - a.count);
  }, [brands]);

  return (
    <View style={{ paddingTop: 34 }}>
      <Wrap>
        <SectionHead title={HOME.brandIntro.title} lead={HOME.brandIntro.text} center />
      </Wrap>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ paddingHorizontal: ui.edge, paddingBottom: 8 }}>
        {tiles.map((b) => (
          <Press key={b.name} onPress={() => go.shop({ brands: b.names })} style={styles.brandTile} accessibilityLabel={b.name}>
            <View style={styles.brandLogo}>
              {BRAND_LOGOS[b.name] ? (
                <Img src={BRAND_LOGOS[b.name]} plain fit="contain" style={{ width: 52, height: 40 }} />
              ) : (
                <T v="h3" color={C.ink2}>{b.name.slice(0, 2).toUpperCase()}</T>
              )}
            </View>
            <T v="small" weight="800" lines={1} center>{b.name}</T>
            <T v="small" size={11} color={C.muted} weight="500" center>{b.count} {b.count === 1 ? 'car' : 'cars'}</T>
          </Press>
        ))}
      </ScrollView>
    </View>
  );
}

/* ------------------------------------------------------------------ stats */

function Stats() {
  const ui = useUI();
  const go = useGo();
  const w = ui.tablet ? '23.5%' : '48.5%';
  return (
    <Wrap style={{ paddingTop: 26, paddingBottom: 30 }}>
      <View style={styles.grid}>
        {HOME.stats.items.map(([n, label]) => (
          <View key={label} style={[styles.statTile, { width: w }]}>
            <T v="display" size={28} color={C.red} center>{n}</T>
            <T v="label" size={10.5} color={C.ink2} center style={{ marginTop: 4 }}>{label}</T>
          </View>
        ))}
      </View>
      <T v="h2" center style={{ marginTop: 10 }}>{HOME.stats.title}</T>
      <T v="sub" color={C.muted} center style={{ marginTop: 8 }}>{HOME.stats.text}</T>
      <Button title={HOME.stats.button} kind="outline" size="sm" onPress={() => go.go('About')} style={{ alignSelf: 'center', marginTop: 16, minWidth: 160 }} />
    </Wrap>
  );
}

/* ------------------------------------------------- a row of vehicle cards */

function Rail({ title, lead, items, tint }) {
  const ui = useUI();
  const go = useGo();
  const w = ui.tablet ? 270 : Math.round(Math.min(250, Math.max(196, ui.width * 0.62)));
  if (items && !items.length) return null;
  return (
    <View style={{ paddingVertical: 28, backgroundColor: tint ? '#ECEEF2' : C.bg }}>
      <Wrap>
        <SectionHead title={title} lead={lead} action="View all" onAction={() => go.shop({})} />
      </Wrap>
      <FlatList
        horizontal
        data={items || []}
        keyExtractor={(p) => p.slug}
        showsHorizontalScrollIndicator={false}
        snapToInterval={w + 12}
        decelerationRate="fast"
        contentContainerStyle={{ paddingHorizontal: ui.edge, paddingBottom: 10 }}
        ItemSeparatorComponent={() => <View style={{ width: 12 }} />}
        renderItem={({ item }) => <ProductCard product={item} width={w} />}
        initialNumToRender={3}
        windowSize={5}
      />
    </View>
  );
}

/* ---------------------------------------------------------- why choose us */

function WhyChoose() {
  const ui = useUI();
  const w = ui.tablet ? 300 : Math.round(Math.min(290, ui.width * 0.74));
  return (
    <LinearGradient colors={GRAD.dark} style={{ paddingVertical: 34 }}>
      <Wrap>
        <T v="h1" color={C.white} center style={{ marginBottom: 20 }}>{HOME.why.title}</T>
      </Wrap>
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        snapToInterval={w + 12}
        decelerationRate="fast"
        contentContainerStyle={{ paddingHorizontal: ui.edge }}
      >
        {HOME.why.items.map((item, i) => (
          <LinearGradient
            key={item.title}
            colors={GRAD.redDeep}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 1 }}
            style={[styles.whyCard, { width: w, marginRight: i < HOME.why.items.length - 1 ? 12 : 0 }]}
          >
            <View style={styles.whyIcon}>
              <Ionicons name={item.icon} size={24} color={C.red} />
            </View>
            <T v="h3" color={C.white} style={{ marginTop: 14 }}>{item.title}</T>
            <T v="sub" color="rgba(255,255,255,0.86)" style={{ marginTop: 8 }}>{item.text}</T>
          </LinearGradient>
        ))}
      </ScrollView>
    </LinearGradient>
  );
}

/* ------------------------------------------------- memberships, payment */

function Members() {
  const ui = useUI();
  const m = HOME.members;
  const p = HOME.payment;
  return (
    <Wrap style={{ paddingVertical: 32 }}>
      <T v="h2" color={C.red} center style={{ marginBottom: 16 }}>{m.title}</T>
      <View style={[styles.grid, { justifyContent: 'center' }]}>
        {m.items.map((it) => (
          <View key={it.name} style={[styles.memberCard, { width: ui.width < 360 ? '100%' : '31.5%' }]}>
            <Img src={it.image} plain fit="contain" style={{ width: '100%', height: 42 }} />
            <T v="label" size={9.5} color={C.ink2} center style={{ marginTop: 10 }} lines={2}>{it.name}</T>
          </View>
        ))}
      </View>
      <T v="h2" style={{ marginTop: 18, marginBottom: 14 }}>{p.title}</T>
      <LinearGradient colors={GRAD.redDeep} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.payDark}>
        <T v="h3" color={C.white}>{p.transparentTitle}</T>
        <T v="sub" color="rgba(255,255,255,0.88)" style={{ marginTop: 8 }}>{p.transparentText}</T>
      </LinearGradient>
      <Card style={{ marginTop: 12 }}>
        <T v="sub" color={C.muted}>{p.lead}</T>
        <T v="h3" style={{ marginTop: 8 }}>
          {p.optionsTitle} <T v="h3" color={C.red}>{p.optionsBrand}</T>
        </T>
        <View style={{ flexDirection: 'row', alignItems: 'center', marginTop: 14, columnGap: 18 }}>
          {p.logos.map((l, i) => (
            <Img key={i} src={l} plain fit="contain" style={{ width: 64, height: 32 }} />
          ))}
        </View>
      </Card>
    </Wrap>
  );
}

/* -------------------------------------------------------- 3 easy steps */

function Steps() {
  const ui = useUI();
  const s = HOME.steps;
  return (
    <Wrap style={{ paddingVertical: 32 }}>
      <SectionHead eyebrow={s.eyebrow} title={s.title} center />
      <View style={ui.tablet ? { flexDirection: 'row', columnGap: 12 } : null}>
        {s.items.map((it, i) => (
          <Card key={it.title} style={[{ flexDirection: 'row', marginBottom: 12 }, ui.tablet && { flex: 1, flexDirection: 'column' }]}>
            <LinearGradient colors={GRAD.red} style={styles.stepIcon}>
              <Ionicons name={it.icon} size={24} color={C.white} />
            </LinearGradient>
            <View style={ui.tablet ? { marginTop: 12 } : { flex: 1, marginLeft: 14 }}>
              <T v="label" size={10} color={C.red}>Step {i + 1}</T>
              <T v="h3" style={{ marginTop: 2 }}>{it.title}</T>
              <T v="sub" color={C.muted} style={{ marginTop: 4 }}>{it.text}</T>
            </View>
          </Card>
        ))}
      </View>
    </Wrap>
  );
}

/* -------------------------------------------------- we bid for you banner */

function Auction() {
  const ui = useUI();
  const go = useGo();
  const a = HOME.auction;
  return (
    <Wrap style={{ paddingBottom: 32 }}>
      <LinearGradient colors={GRAD.red} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.banner}>
        <Eyebrow color="#FFD5D6">{a.eyebrow}</Eyebrow>
        <T v="h2" color={C.white}>{a.title}</T>
        <T v="sub" color="rgba(255,255,255,0.88)" style={{ marginTop: 8 }}>{a.text}</T>
        <Img src={a.image} plain fit="contain" style={{ width: '100%', height: ui.s(150), marginTop: 6 }} />
        <Button title={a.button} kind="light" onPress={() => go.contact('Auction support request')} full />
      </LinearGradient>
    </Wrap>
  );
}

/* ------------------------------------------------------------ body types */

function BodyTypes() {
  const ui = useUI();
  const go = useGo();
  const b = HOME.bodyTypes;
  const w = ui.tablet ? '23.5%' : '48.5%';
  return (
    <View style={{ paddingVertical: 32, backgroundColor: C.card }}>
      <Wrap>
        <SectionHead eyebrow={b.eyebrow} title={b.title} center />
        <View style={styles.grid}>
          {b.items.map((it) => (
            <Press key={it.name} onPress={() => go.shop(it.filter)} style={[styles.bodyTile, { width: w }]} accessibilityLabel={it.name}>
              <Img src={it.image} plain fit="contain" style={{ width: '100%', height: ui.s(64) }} />
              <T v="title" center style={{ marginTop: 8 }}>{it.name}</T>
            </Press>
          ))}
        </View>
        <Button title={b.button} kind="dark" onPress={() => go.shop({})} style={{ alignSelf: 'center', minWidth: 200, marginTop: 6 }} />
      </Wrap>
    </View>
  );
}

/* --------------------------------------------------------- client videos */

function Videos() {
  const ui = useUI();
  const go = useGo();
  const v = HOME.videos;
  const h = ui.s(250);
  return (
    <View style={{ paddingVertical: 32 }}>
      <Wrap>
        <SectionHead title={v.title} />
      </Wrap>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ paddingHorizontal: ui.edge }}>
        {v.items.map((it, i) => (
          <Press
            key={it.id}
            onPress={() => go.go('Video', { path: it.path })}
            style={[styles.video, { height: h, width: Math.round(h * (it.ratio >= 1 ? 0.8 : 0.5625)), marginRight: i < v.items.length - 1 ? 12 : 0 }]}
            accessibilityLabel={'Play client video ' + (i + 1)}
          >
            <Img src={it.poster} style={StyleSheet.absoluteFill} />
            <LinearGradient colors={['transparent', 'rgba(11,12,15,0.7)']} style={StyleSheet.absoluteFill} />
            <View style={styles.play}>
              <Ionicons name="play" size={24} color={C.white} style={{ marginLeft: 3 }} />
            </View>
          </Press>
        ))}
      </ScrollView>
    </View>
  );
}

/* ----------------------------------------------------------- testimonial */

function Testimonials() {
  const t = HOME.testimonials;
  return (
    <View style={{ backgroundColor: C.card, paddingVertical: 32 }}>
      <Wrap>
        <SectionHead eyebrow={t.eyebrow} title={t.title} lead={t.text} />
        {t.items.map((it) => (
          <View key={it.name} style={styles.quoteCard}>
            <Ionicons name="chatbox-ellipses" size={26} color={C.red} />
            <T v="body" color={C.ink2} style={{ marginTop: 10 }}>{it.quote}</T>
            <View style={styles.person}>
              <View style={styles.avatar}>
                <T v="title" color={C.white}>{it.initials}</T>
              </View>
              <View style={{ marginLeft: 12 }}>
                <T v="title">{it.name}</T>
                <T v="small" color={C.muted} weight="500">{it.place}</T>
              </View>
            </View>
          </View>
        ))}
      </Wrap>
    </View>
  );
}

/* ------------------------------------------------------- register banner */

function Register() {
  const ui = useUI();
  const go = useGo();
  const account = useAccount();
  const r = HOME.register;
  return (
    <Wrap style={{ paddingVertical: 32 }}>
      <LinearGradient colors={GRAD.dark} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={styles.banner}>
        <Eyebrow color="#FF5A60">{r.eyebrow}</Eyebrow>
        <T v="h2" color={C.white}>{r.title}</T>
        <T v="sub" color="rgba(255,255,255,0.8)" style={{ marginTop: 8 }}>{r.text}</T>
        <Img src={r.image} plain fit="contain" style={{ width: '100%', height: ui.s(140), marginTop: 6 }} />
        <Button
          title={account.user ? 'Browse Vehicles' : r.button}
          onPress={() => (account.user ? go.shop() : go.go('Register'))}
          full
        />
      </LinearGradient>
    </Wrap>
  );
}

/* ---------------------------------------------------------------- guides */

function Guides({ blogs }) {
  const ui = useUI();
  const go = useGo();
  const g = HOME.guides;
  const w = ui.tablet ? 300 : Math.round(Math.min(280, ui.width * 0.72));
  if (!blogs.length) return null;
  return (
    <View style={{ paddingBottom: 32 }}>
      <Wrap>
        <SectionHead eyebrow={g.eyebrow} title={g.title} action={g.button} onAction={() => go.go('Blog')} />
      </Wrap>
      <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ paddingHorizontal: ui.edge, paddingBottom: 8 }}>
        {blogs.map((b, i) => (
          <Press
            key={b.slug}
            onPress={() => go.go('BlogPost', { slug: b.slug })}
            style={[styles.blogCard, { width: w, marginRight: i < blogs.length - 1 ? 12 : 0 }]}
            accessibilityLabel={b.title}
          >
            <Img src={b.image} style={{ width: '100%', aspectRatio: 16 / 9 }} />
            <View style={{ padding: 14 }}>
              <T v="small" color={C.red} weight="800">{b.date}</T>
              <T v="title" lines={3} style={{ marginTop: 6 }}>{b.title}</T>
            </View>
          </Press>
        ))}
      </ScrollView>
    </View>
  );
}

/* ------------------------------------------------------------------- FAQ */

function Faq() {
  const go = useGo();
  const [open, setOpen] = useState(0);
  return (
    <View style={{ backgroundColor: C.card, paddingVertical: 32 }}>
      <Wrap>
        <SectionHead title={FAQ.title} />
        {FAQ.questions.map((q, i) => (
          <View key={q} style={styles.faqItem}>
            <Press onPress={() => setOpen(open === i ? -1 : i)} style={styles.faqQ} accessibilityLabel={q}>
              <T v="title" style={{ flex: 1, paddingRight: 10 }}>{q}</T>
              <Ionicons name={open === i ? 'remove' : 'add'} size={20} color={C.red} />
            </Press>
            {open === i ? <T v="sub" color={C.muted} style={{ paddingBottom: 14 }}>{FAQ.answer}</T> : null}
          </View>
        ))}
        <Button title="Ask Our Team" icon="chatbubbles-outline" kind="outline" onPress={() => go.chat()} style={{ marginTop: 18 }} />
      </Wrap>
    </View>
  );
}

/* --------------------------------------------------------- talk to us */

function Help() {
  return (
    <Wrap style={{ paddingVertical: 28 }}>
      <Card>
        <T v="h3">Talk to SBK</T>
        <T v="sub" color={C.muted} style={{ marginTop: 4 }}>{CONTACT.hours}</T>
        <View style={{ flexDirection: 'row', columnGap: 10, marginTop: 14 }}>
          <HelpBtn icon="logo-whatsapp" label="WhatsApp" onPress={() => whatsApp()} color="#1FAF5A" />
          <HelpBtn icon="call" label="Call" onPress={() => callNumber(CONTACT.dial)} color={C.ink} />
          <HelpBtn icon="mail" label="Email" onPress={() => emailTo(CONTACT.email)} color={C.red} />
        </View>
      </Card>
    </Wrap>
  );
}

function HelpBtn({ icon, label, onPress, color }) {
  return (
    <Press onPress={onPress} style={styles.helpBtn} accessibilityLabel={label}>
      <View style={[styles.helpIcon, { backgroundColor: color }]}>
        <Ionicons name={icon} size={20} color={C.white} />
      </View>
      <T v="small" weight="800" style={{ marginTop: 6 }}>{label}</T>
    </Press>
  );
}

/* ---------------------------------------------------------------- footer */

function Footer() {
  const ui = useUI();
  return (
    <View style={{ backgroundColor: C.dark, paddingVertical: 30 }}>
      <Wrap>
        <Img src={LOGO} plain fit="contain" style={{ width: ui.s(150), height: ui.s(52) }} />
        <T v="sub" color="rgba(255,255,255,0.7)" style={{ marginTop: 12 }}>{FOOTER_TEXT}</T>
        <T v="small" color="rgba(255,255,255,0.45)" weight="500" style={{ marginTop: 18 }}>Copyright 2026 SBKAutotrading All Rights Reserved</T>
      </Wrap>
    </View>
  );
}

const styles = StyleSheet.create({
  heroSpecs: { flexDirection: 'row', flexWrap: 'wrap', marginTop: 14, gap: 8 },
  heroSpec: { borderWidth: 1, borderColor: 'rgba(255,255,255,0.4)', borderRadius: R.xs, paddingHorizontal: 12, paddingVertical: 7, backgroundColor: 'rgba(255,255,255,0.06)' },
  heroButtons: { flexDirection: 'row', marginTop: 18 },
  stamp: {
    position: 'absolute',
    width: 74,
    height: 74,
    borderRadius: 37,
    backgroundColor: C.red,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 3,
    borderColor: 'rgba(255,255,255,0.85)',
  },
  dots: { position: 'absolute', bottom: 40, left: 0, right: 0, flexDirection: 'row', justifyContent: 'center', columnGap: 6 },
  dot: { width: 8, height: 8, borderRadius: 4, backgroundColor: 'rgba(255,255,255,0.45)' },
  dotOn: { width: 22, backgroundColor: C.white },
  searchCard: { backgroundColor: C.card, borderRadius: 14, padding: 18, paddingTop: 22, overflow: 'hidden', ...lift, elevation: 6 },
  searchLine: { position: 'absolute', top: 0, left: 0, right: 0, height: 4 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between' },
  quick: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginTop: 14 },
  quickChip: { minHeight: 34, paddingHorizontal: 12, borderRadius: R.pill, backgroundColor: '#F1F2F5', justifyContent: 'center' },
  brandTile: { width: 116, alignItems: 'center', backgroundColor: C.card, borderRadius: R.lg, paddingVertical: 12, paddingHorizontal: 6, marginRight: 10, borderWidth: 1, borderColor: C.line2 },
  brandLogo: { width: 60, height: 46, alignItems: 'center', justifyContent: 'center', marginBottom: 6 },
  statTile: { backgroundColor: C.card, borderRadius: R.lg, paddingVertical: 18, paddingHorizontal: 8, marginBottom: 12, borderWidth: 1, borderColor: C.line2 },
  whyCard: { borderRadius: R.lg, padding: 20, minHeight: 230 },
  whyIcon: { width: 50, height: 50, borderRadius: 25, backgroundColor: C.white, alignItems: 'center', justifyContent: 'center' },
  memberCard: { backgroundColor: C.card, borderRadius: R.md, paddingVertical: 14, paddingHorizontal: 10, marginBottom: 10, alignItems: 'center', borderWidth: 1, borderColor: C.line2 },
  payDark: { borderRadius: R.lg, padding: 20 },
  stepIcon: { width: 54, height: 54, borderRadius: 14, alignItems: 'center', justifyContent: 'center' },
  banner: { borderRadius: R.xl, padding: 22, overflow: 'hidden' },
  bodyTile: { backgroundColor: C.card, borderRadius: R.md, borderWidth: 1.5, borderColor: C.redLine, paddingVertical: 14, paddingHorizontal: 10, marginBottom: 12, alignItems: 'center' },
  video: { borderRadius: R.lg, overflow: 'hidden', backgroundColor: C.dark, alignItems: 'center', justifyContent: 'center' },
  play: { width: 56, height: 56, borderRadius: 28, backgroundColor: 'rgba(215,25,32,0.92)', alignItems: 'center', justifyContent: 'center' },
  quoteCard: { backgroundColor: C.bg, borderRadius: R.lg, padding: 18, borderLeftWidth: 4, borderLeftColor: C.red },
  person: { flexDirection: 'row', alignItems: 'center', marginTop: 16 },
  avatar: { width: 44, height: 44, borderRadius: 22, backgroundColor: C.red, alignItems: 'center', justifyContent: 'center' },
  blogCard: { backgroundColor: C.card, borderRadius: R.lg, overflow: 'hidden', ...lift },
  faqItem: { borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  faqQ: { flexDirection: 'row', alignItems: 'center', minHeight: 56, paddingVertical: 10 },
  helpBtn: { flex: 1, alignItems: 'center', paddingVertical: 10, borderRadius: R.md, backgroundColor: C.bg },
  helpIcon: { width: 42, height: 42, borderRadius: 21, alignItems: 'center', justifyContent: 'center' },
});
