/* Shop - the website's stock list: search, filters, sort, and the cards. */
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { ActivityIndicator, FlatList, RefreshControl, ScrollView, StyleSheet, TextInput, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, MAX_FONT, R, useUI } from '../theme';
import * as api from '../api';
import { MILEAGES, PRICE_RANGES, SORTS, TRANSMISSIONS } from '../api/static';
import { Badge, IconButton, Press, T } from '../components/base';
import { Empty, TabHeader } from '../components/layout';
import Sheet from '../components/Sheet';
import ProductCard from '../components/ProductCard';
import { countActive, EMPTY, useFilters } from '../store/filters';
import { useShop } from '../store/shop';
import { useGo } from '../nav';
import { money } from '../format';

const GAP = 12;

export default function ShopScreen() {
  const ui = useUI();
  const go = useGo();
  const nav = useNavigation();
  const shop = useShop();
  const { filters, stamp, patch, setFilters } = useFilters();
  const [result, setResult] = useState(null);
  const [more, setMore] = useState(false);
  const [failed, setFailed] = useState('');
  const [text, setText] = useState(filters.search);
  const [sortOpen, setSortOpen] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const list = useRef(null);

  // Typing filters the list as you go, a moment after the last key.
  useEffect(() => { setText(filters.search); }, [filters.search]);
  useEffect(() => {
    const t = setTimeout(() => { if (text !== filters.search) patch({ search: text }); }, 280);
    return () => clearTimeout(t);
  }, [text, filters.search, patch]);

  // The first page for these filters; a newer request always wins.
  const seq = useRef(0);
  const load = useCallback(async () => {
    const mine = ++seq.current;
    setFailed('');
    try {
      const r = await api.getProducts(filters, 1);
      if (mine === seq.current) setResult(r);
    } catch (e) {
      if (mine === seq.current) setFailed(e.message || 'Could not load vehicles.');
    }
  }, [filters]);

  useEffect(() => { load(); }, [load]);
  useEffect(() => { list.current?.scrollToOffset({ offset: 0, animated: false }); }, [stamp]);

  // The next page when the list nears its end.
  const loadMore = async () => {
    if (!result || more || result.page >= result.lastPage) return;
    const mine = seq.current;
    setMore(true);
    try {
      const r = await api.getProducts(filters, result.page + 1);
      if (mine !== seq.current) return;
      setResult((prev) => {
        const have = new Set(prev.items.map((p) => p.slug));
        return { ...r, items: [...prev.items, ...r.items.filter((p) => !have.has(p.slug))] };
      });
    } catch (e) {
      // Scrolling again tries again.
    } finally {
      setMore(false);
    }
  };

  const refresh = async () => {
    setRefreshing(true);
    await api.refresh();
    await load();
    setRefreshing(false);
  };

  const cols = ui.cols;
  const cardW = Math.floor((ui.inner - GAP * (cols - 1)) / cols);
  const active = countActive(filters);
  const chips = useMemo(() => chipsFor(filters), [filters]);
  const sortLabel = (SORTS.find((s) => s.value === filters.sort) || SORTS[0]).label;

  return (
    <View style={{ flex: 1, backgroundColor: C.bg }}>
      <TabHeader
        title="Shop"
        sub={result ? result.total + ' vehicles found' : ' '}
        right={<IconButton name="heart-outline" onPress={() => go.go('Wishlist')} badge={shop.wish.length} label="Wishlist" />}
      />
      <View style={styles.bar}>
        <View style={[styles.barRow, { width: ui.col, paddingHorizontal: ui.gutter }]}>
          <View style={[styles.search, { minHeight: ui.tap(46) }]}>
            <Ionicons name="search" size={18} color={C.faint} style={{ marginLeft: 12 }} />
            <TextInput
              value={text}
              onChangeText={setText}
              placeholder="Search here..."
              placeholderTextColor={C.faint}
              returnKeyType="search"
              onSubmitEditing={() => patch({ search: text })}
              style={[styles.searchInput, { fontSize: ui.t(15) }]}
              maxFontSizeMultiplier={MAX_FONT}
              selectionColor={C.red}
              cursorColor={C.red}
              autoCorrect={false}
            />
            {text ? (
              <Press onPress={() => { setText(''); patch({ search: '' }); }} style={styles.clear} accessibilityLabel="Clear search" borderless>
                <Ionicons name="close-circle" size={18} color={C.faint} />
              </Press>
            ) : null}
          </View>
          <View>
            <Press onPress={() => nav.navigate('Filters')} style={[styles.tool, active > 0 && { backgroundColor: C.ink, borderColor: C.ink }]} accessibilityLabel="Filters">
              <Ionicons name="options-outline" size={20} color={active > 0 ? C.white : C.ink} />
            </Press>
            <Badge count={active} style={{ position: 'absolute', top: -5, right: -5 }} />
          </View>
          <Press onPress={() => setSortOpen(true)} style={styles.tool} accessibilityLabel="Sort">
            <Ionicons name="swap-vertical" size={20} color={C.ink} />
          </Press>
        </View>
        {chips.length || filters.sort !== 'default' ? (
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ paddingHorizontal: ui.gutter, paddingBottom: 10, columnGap: 8 }}>
            {filters.sort !== 'default' ? (
              <View style={[styles.activeChip, { backgroundColor: C.line2 }]}>
                <Ionicons name="swap-vertical" size={13} color={C.ink2} style={{ marginRight: 4 }} />
                <T v="small" weight="700" color={C.ink2}>{sortLabel}</T>
              </View>
            ) : null}
            {chips.map((c) => (
              <Press key={c.key} onPress={() => patch(c.remove(filters))} style={styles.activeChip} accessibilityLabel={'Remove ' + c.label}>
                <T v="small" weight="700" color={C.red}>{c.label}</T>
                <Ionicons name="close" size={14} color={C.red} style={{ marginLeft: 4 }} />
              </Press>
            ))}
            {chips.length > 1 ? (
              <Press onPress={() => setFilters({ ...EMPTY, search: filters.search, sort: filters.sort })} style={[styles.activeChip, { borderColor: C.line, backgroundColor: C.card }]}>
                <T v="small" weight="700" color={C.ink2}>Clear all</T>
              </Press>
            ) : null}
          </ScrollView>
        ) : null}
      </View>

      <FlatList
        ref={list}
        key={'cols-' + cols}
        data={result ? result.items : []}
        numColumns={cols}
        keyExtractor={(p) => p.slug}
        contentContainerStyle={{ width: ui.col, alignSelf: 'center', paddingHorizontal: ui.gutter, paddingTop: 14, paddingBottom: 28 }}
        columnWrapperStyle={cols > 1 ? { columnGap: GAP } : undefined}
        ItemSeparatorComponent={() => <View style={{ height: GAP }} />}
        renderItem={({ item }) => <ProductCard product={item} width={cardW} />}
        initialNumToRender={6}
        windowSize={7}
        keyboardDismissMode="on-drag"
        keyboardShouldPersistTaps="handled"
        refreshControl={<RefreshControl refreshing={refreshing} onRefresh={refresh} colors={[C.red]} tintColor={C.red} />}
        onEndReached={loadMore}
        onEndReachedThreshold={0.6}
        ListFooterComponent={more ? <ActivityIndicator color={C.red} style={{ marginVertical: 18 }} /> : null}
        ListEmptyComponent={failed ? (
          <Empty icon="cloud-offline-outline" title="Could not load vehicles" text={failed} action="Try Again" onAction={load} />
        ) : result ? (
          <Empty
            icon="search-outline"
            title="No vehicles match"
            text={filters.search ? 'Nothing in stock matches "' + filters.search + '" right now. Our team can source it for you from Japan.' : 'Nothing in stock matches these filters right now. Our team can source it for you from Japan.'}
            action="Ask SBK to Source It"
            onAction={() => go.chat('Hi SBK, I am looking for ' + (filters.search || 'a vehicle') + '. Can you source one for me?')}
            second="Reset Filters"
            onSecond={() => setFilters({})}
          />
        ) : null}
      />

      <Sheet open={sortOpen} onClose={() => setSortOpen(false)} title="Sort by">
        {SORTS.map((s) => {
          const on = s.value === filters.sort;
          return (
            <Press key={s.value} onPress={() => { patch({ sort: s.value }); setSortOpen(false); }} style={styles.sortRow}>
              <T v="body" weight={on ? '800' : '500'} color={on ? C.red : C.ink} style={{ flex: 1 }}>{s.label}</T>
              {on ? <Ionicons name="checkmark-circle" size={20} color={C.red} /> : null}
            </Press>
          );
        })}
      </Sheet>
    </View>
  );
}

/* The filters that are on, each as a chip that takes itself off. */
function chipsFor(f) {
  const out = [];
  f.brands.forEach((b) => out.push({ key: 'b' + b, label: b, remove: (x) => ({ brands: x.brands.filter((v) => v !== b) }) }));
  if (f.model) out.push({ key: 'm', label: f.model, remove: () => ({ model: null }) });
  f.types.forEach((t) => out.push({ key: 't' + t, label: t, remove: (x) => ({ types: x.types.filter((v) => v !== t) }) }));
  f.grades.forEach((g) => out.push({ key: 'g' + g, label: 'Grade ' + g, remove: (x) => ({ grades: x.grades.filter((v) => v !== g) }) }));
  if (f.year) out.push({ key: 'y', label: String(f.year), remove: () => ({ year: null }) });
  if (f.maxMileage) {
    const m = MILEAGES.find((x) => x.value === f.maxMileage);
    out.push({ key: 'k', label: m ? m.label : 'Under ' + f.maxMileage + ' km', remove: () => ({ maxMileage: null }) });
  }
  if (f.transmission) {
    const t = TRANSMISSIONS.find((x) => x.value === f.transmission);
    out.push({ key: 'tr', label: t ? t.value : f.transmission, remove: () => ({ transmission: null }) });
  }
  const hasMin = f.minPrice !== '' && f.minPrice != null;
  const hasMax = f.maxPrice !== '' && f.maxPrice != null;
  if (hasMin || hasMax) {
    const preset = PRICE_RANGES.find((p) => String(p.min) === String(f.minPrice) && String(p.max || '') === String(f.maxPrice || ''));
    const label = preset ? preset.label : (hasMin ? money(f.minPrice).replace('.00', '') : '$0') + ' - ' + (hasMax ? money(f.maxPrice).replace('.00', '') : 'any');
    out.push({ key: 'p', label, remove: () => ({ minPrice: '', maxPrice: '' }) });
  }
  return out;
}

const styles = StyleSheet.create({
  bar: { backgroundColor: C.card, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line, alignItems: 'center' },
  barRow: { flexDirection: 'row', alignItems: 'center', columnGap: 8, paddingVertical: 10 },
  search: { flex: 1, flexDirection: 'row', alignItems: 'center', backgroundColor: C.bg, borderRadius: R.md, borderWidth: 1, borderColor: C.line },
  searchInput: { flex: 1, color: C.ink, paddingHorizontal: 10, paddingVertical: 8 },
  clear: { width: 40, height: 40, alignItems: 'center', justifyContent: 'center' },
  tool: { width: 46, height: 46, borderRadius: R.md, borderWidth: 1, borderColor: C.line, backgroundColor: C.card, alignItems: 'center', justifyContent: 'center', overflow: 'hidden' },
  activeChip: { flexDirection: 'row', alignItems: 'center', minHeight: 32, paddingHorizontal: 12, borderRadius: R.pill, backgroundColor: C.redWash, borderWidth: 1, borderColor: C.redLine },
  sortRow: { flexDirection: 'row', alignItems: 'center', minHeight: 54, paddingHorizontal: 20, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line2 },
});
