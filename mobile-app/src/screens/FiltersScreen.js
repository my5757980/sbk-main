/* Filters - every filter of the website's shop sidebar, as a full sheet.
   Changes are a draft until "Show N vehicles"; Back leaves the shop as it was. */
import { useEffect, useLayoutEffect, useMemo, useState } from 'react';
import { ScrollView, StyleSheet, TextInput, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { C, MAX_FONT, R, useUI } from '../theme';
import * as api from '../api';
import { MILEAGES, PRICE_RANGES, TRANSMISSIONS } from '../api/static';
import { Button, Press, T } from '../components/base';
import { Wrap } from '../components/layout';
import { Chip, Select } from '../components/form';
import { EMPTY, useFilters } from '../store/filters';
import { money } from '../format';

export default function FiltersScreen() {
  const ui = useUI();
  const nav = useNavigation();
  const insets = useSafeAreaInsets();
  const { filters, setFilters } = useFilters();
  const [draft, setDraft] = useState(filters);
  const [options, setOptions] = useState(null);
  const [count, setCount] = useState(null);

  useEffect(() => { api.getFilterOptions().then(setOptions); }, []);
  useEffect(() => {
    let live = true;
    api.getProducts(draft).then((r) => live && setCount(r.total));
    return () => { live = false; };
  }, [draft]);

  const set = (part) => setDraft((d) => ({ ...d, ...part }));
  const toggle = (key, v) => setDraft((d) => ({ ...d, [key]: d[key].includes(v) ? d[key].filter((x) => x !== v) : [...d[key], v] }));
  const clearAll = () => setDraft({ ...EMPTY, search: draft.search, sort: draft.sort });

  useLayoutEffect(() => {
    nav.setOptions({
      headerRight: () => (
        <Press onPress={clearAll} style={{ minHeight: 40, justifyContent: 'center', paddingHorizontal: 6 }} accessibilityLabel="Reset filters">
          <T v="title" color={C.red} weight="800">Reset</T>
        </Press>
      ),
    });
  }, [nav, draft.search, draft.sort]);

  const models = useMemo(() => {
    if (!options) return [];
    return options.models
      .filter((m) => !draft.brands.length || draft.brands.includes(m.brand))
      .map((m) => ({ label: draft.brands.length === 1 ? m.name : m.brand + ' - ' + m.name, value: m.name }));
  }, [options, draft.brands]);

  const presetIndex = PRICE_RANGES.findIndex((p) => String(p.min) === String(draft.minPrice) && String(p.max || '') === String(draft.maxPrice || ''));

  return (
    <View style={{ flex: 1, backgroundColor: C.card }}>
      <ScrollView keyboardShouldPersistTaps="handled" contentContainerStyle={{ paddingBottom: 24 }}>
        <Wrap style={{ paddingTop: 8 }}>
          <Group title="Brand">
            <View style={styles.chips}>
              {(options ? options.brands : []).map((b) => (
                <Chip key={b} label={b} selected={draft.brands.includes(b)} onPress={() => { toggle('brands', b); set({ model: null }); }} />
              ))}
            </View>
          </Group>

          <Group title="Model">
            <Select value={draft.model} anyLabel="All Models" options={models} onChange={(v) => set({ model: v })} searchable style={{ marginBottom: 0 }} />
          </Group>

          <Group title="Body Type">
            <View style={styles.chips}>
              {(options ? options.types : []).map((t) => (
                <Chip key={t} label={t} selected={draft.types.includes(t)} onPress={() => toggle('types', t)} />
              ))}
            </View>
          </Group>

          <Group title="Price (USD)">
            <View style={styles.priceRow}>
              <PriceBox placeholder="Min" value={draft.minPrice} onChange={(v) => set({ minPrice: v })} />
              <T v="title" color={C.faint} style={{ marginHorizontal: 10 }}>–</T>
              <PriceBox placeholder="Max" value={draft.maxPrice} onChange={(v) => set({ maxPrice: v })} />
            </View>
            <View style={[styles.chips, { marginTop: 12 }]}>
              {PRICE_RANGES.map((p, i) => (
                <Chip
                  key={p.label}
                  label={p.label}
                  small
                  selected={presetIndex === i}
                  onPress={() => (presetIndex === i ? set({ minPrice: '', maxPrice: '' }) : set({ minPrice: String(p.min), maxPrice: p.max ? String(p.max) : '' }))}
                />
              ))}
            </View>
            {options ? <T v="small" color={C.muted} weight="500">The most expensive vehicle in stock is {money(options.maxPrice).replace('.00', '')}.</T> : null}
          </Group>

          <Group title="Year">
            <Select
              value={draft.year}
              anyLabel="All Years"
              options={(options ? options.years : []).map((y) => ({ label: String(y), value: y }))}
              onChange={(v) => set({ year: v })}
              style={{ marginBottom: 0 }}
            />
          </Group>

          <Group title="Mileage">
            <View style={styles.chips}>
              <Chip label="Any Mileage" selected={!draft.maxMileage} onPress={() => set({ maxMileage: null })} />
              {MILEAGES.map((m) => (
                <Chip key={m.value} label={m.label} selected={draft.maxMileage === m.value} onPress={() => set({ maxMileage: draft.maxMileage === m.value ? null : m.value })} />
              ))}
            </View>
          </Group>

          <Group title="Transmission">
            <View style={styles.chips}>
              <Chip label="Any Transmission" selected={!draft.transmission} onPress={() => set({ transmission: null })} />
              {TRANSMISSIONS.map((t) => (
                <Chip key={t.value} label={t.label} selected={draft.transmission === t.value} onPress={() => set({ transmission: draft.transmission === t.value ? null : t.value })} />
              ))}
            </View>
          </Group>

          <Group title="Grade" last>
            <View style={styles.chips}>
              {(options ? options.grades : []).map((g) => (
                <Chip key={g} label={g} selected={draft.grades.includes(g)} onPress={() => toggle('grades', g)} />
              ))}
            </View>
          </Group>
        </Wrap>
      </ScrollView>
      <View style={[styles.footer, { paddingBottom: Math.max(insets.bottom, 12) }]}>
        <Wrap>
          <Button
            title={count == null ? 'Show vehicles' : count === 0 ? 'No vehicles match' : 'Show ' + count + (count === 1 ? ' vehicle' : ' vehicles')}
            onPress={() => { setFilters(draft); nav.goBack(); }}
            disabled={count === 0}
            full
          />
        </Wrap>
      </View>
    </View>
  );
}

function Group({ title, children, last }) {
  return (
    <View style={[styles.group, last && { borderBottomWidth: 0 }]}>
      <T v="label" color={C.ink} style={{ marginBottom: 12 }}>{title}</T>
      {children}
    </View>
  );
}

function PriceBox({ placeholder, value, onChange }) {
  const ui = useUI();
  return (
    <View style={[styles.priceBox, { minHeight: ui.tap(48) }]}>
      <T v="title" color={C.muted} style={{ marginLeft: 12 }}>$</T>
      <TextInput
        value={value ? String(value) : ''}
        onChangeText={(t) => onChange(t.replace(/[^0-9]/g, ''))}
        placeholder={placeholder}
        placeholderTextColor={C.faint}
        keyboardType="number-pad"
        style={[styles.priceInput, { fontSize: ui.t(15) }]}
        maxFontSizeMultiplier={MAX_FONT}
        selectionColor={C.red}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  group: { paddingVertical: 18, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line },
  chips: { flexDirection: 'row', flexWrap: 'wrap' },
  priceRow: { flexDirection: 'row', alignItems: 'center' },
  priceBox: { flex: 1, flexDirection: 'row', alignItems: 'center', borderWidth: 1, borderColor: '#DFE2E7', borderRadius: R.sm + 2, backgroundColor: C.field },
  priceInput: { flex: 1, color: C.ink, paddingHorizontal: 8, paddingVertical: 8 },
  footer: { borderTopWidth: StyleSheet.hairlineWidth, borderTopColor: C.line, backgroundColor: C.card, paddingTop: 12 },
});
