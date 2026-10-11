/* Form fields in the website's style: a small bold label over a soft box that
   turns SBK red while it is being typed in. */
import { useMemo, useState } from 'react';
import { FlatList, StyleSheet, TextInput, View } from 'react-native';
import Ionicons from '@expo/vector-icons/Ionicons';

import { C, MAX_FONT, R, useUI } from '../theme';
import { Press, T } from './base';
import Sheet from './Sheet';

export function Field({
  label, required, value, onChangeText, placeholder, error, hint, secure, multiline,
  keyboardType, autoComplete, textContentType, autoCapitalize, returnKeyType, onSubmitEditing,
  inputRef, editable = true, style, maxLength, left, onBlur,
}) {
  const ui = useUI();
  const [focus, setFocus] = useState(false);
  const [hidden, setHidden] = useState(true);
  return (
    <View style={[styles.field, style]}>
      {label ? (
        <T v="label" color={C.ink} style={{ marginBottom: 7 }}>
          {label}{required ? <T v="label" color={C.red}> *</T> : null}
        </T>
      ) : null}
      <View
        style={[
          styles.box,
          { minHeight: ui.tap(50) },
          multiline && { minHeight: 120, alignItems: 'flex-start' },
          focus && styles.boxFocus,
          !!error && styles.boxError,
          !editable && { backgroundColor: C.line2 },
        ]}
      >
        {left ? <Ionicons name={left} size={18} color={focus ? C.red : C.faint} style={{ marginLeft: 14 }} /> : null}
        <TextInput
          ref={inputRef}
          value={value}
          onChangeText={onChangeText}
          placeholder={placeholder}
          placeholderTextColor={C.faint}
          secureTextEntry={secure && hidden}
          multiline={multiline}
          keyboardType={keyboardType}
          autoComplete={autoComplete}
          textContentType={textContentType}
          autoCapitalize={autoCapitalize ?? (keyboardType === 'email-address' || secure ? 'none' : 'sentences')}
          autoCorrect={!(keyboardType === 'email-address' || secure)}
          returnKeyType={returnKeyType}
          onSubmitEditing={onSubmitEditing}
          submitBehavior={multiline ? 'newline' : 'blurAndSubmit'}
          editable={editable}
          maxLength={maxLength}
          onFocus={() => setFocus(true)}
          onBlur={() => { setFocus(false); if (onBlur) onBlur(); }}
          maxFontSizeMultiplier={MAX_FONT}
          textAlignVertical={multiline ? 'top' : 'center'}
          style={[styles.input, { fontSize: ui.t(15) }, multiline && { paddingTop: 14, minHeight: 118 }]}
          selectionColor={C.red}
          cursorColor={C.red}
        />
        {secure ? (
          <Press onPress={() => setHidden((h) => !h)} style={styles.eye} hitSlop={8} accessibilityLabel={hidden ? 'Show password' : 'Hide password'} borderless>
            <Ionicons name={hidden ? 'eye-outline' : 'eye-off-outline'} size={20} color={C.muted} />
          </Press>
        ) : null}
      </View>
      {error ? <T v="small" color={C.red} style={{ marginTop: 6 }}>{error}</T> : hint ? <T v="small" color={C.muted} weight="500" style={{ marginTop: 6 }}>{hint}</T> : null}
    </View>
  );
}

/**
 * A choice from a list, opened in a sheet - the app's version of the
 * website's dropdowns. options: [{ label, value }]; value null = "any".
 */
export function Select({ label, value, options, onChange, placeholder, anyLabel, searchable, style }) {
  const ui = useUI();
  const [open, setOpen] = useState(false);
  const [q, setQ] = useState('');
  const current = options.find((o) => o.value === value);
  const list = useMemo(() => {
    const n = q.trim().toLowerCase();
    const base = n ? options.filter((o) => o.label.toLowerCase().includes(n)) : options;
    return anyLabel ? [{ label: anyLabel, value: null }, ...base] : base;
  }, [options, q, anyLabel]);

  return (
    <View style={[styles.field, style]}>
      {label ? <T v="label" color={C.ink} style={{ marginBottom: 7 }}>{label}</T> : null}
      <Press onPress={() => { setQ(''); setOpen(true); }} style={[styles.box, { minHeight: ui.tap(50), paddingRight: 12 }]} accessibilityLabel={label}>
        <T v="body" color={current ? C.ink : C.muted} lines={1} style={{ flex: 1, paddingLeft: 14 }}>
          {current ? current.label : (placeholder || anyLabel || 'Select')}
        </T>
        <Ionicons name="chevron-down" size={18} color={C.red} />
      </Press>
      <Sheet open={open} onClose={() => setOpen(false)} title={label} tall={options.length > 10}>
        {searchable ? (
          <View style={{ paddingHorizontal: 18, paddingBottom: 8 }}>
            <View style={[styles.box, { minHeight: 46 }]}>
              <Ionicons name="search" size={18} color={C.faint} style={{ marginLeft: 12 }} />
              <TextInput
                value={q}
                onChangeText={setQ}
                placeholder="Search"
                placeholderTextColor={C.faint}
                style={[styles.input, { fontSize: ui.t(15) }]}
                maxFontSizeMultiplier={MAX_FONT}
                selectionColor={C.red}
                autoCorrect={false}
              />
            </View>
          </View>
        ) : null}
        <FlatList
          data={list}
          keyExtractor={(o) => String(o.value) + o.label}
          keyboardShouldPersistTaps="handled"
          renderItem={({ item }) => {
            const on = item.value === value || (item.value == null && value == null);
            return (
              <Press onPress={() => { onChange(item.value); setOpen(false); }} style={styles.option}>
                <T v="body" weight={on ? '800' : '500'} color={on ? C.red : C.ink} style={{ flex: 1 }}>{item.label}</T>
                {on ? <Ionicons name="checkmark-circle" size={20} color={C.red} /> : null}
              </Press>
            );
          }}
          ListEmptyComponent={<T v="sub" color={C.muted} center style={{ padding: 24 }}>Nothing matches.</T>}
        />
      </Sheet>
    </View>
  );
}

/** A filter chip: tap to choose, tap again to clear. */
export function Chip({ label, selected, onPress, icon, small, style }) {
  const ui = useUI();
  return (
    <Press
      onPress={onPress}
      style={[
        styles.chip,
        { minHeight: small ? 34 : ui.tap(40) - 2 },
        selected && styles.chipOn,
        style,
      ]}
      accessibilityLabel={label}
    >
      {icon ? <Ionicons name={icon} size={15} color={selected ? C.white : C.ink2} style={{ marginRight: 6 }} /> : null}
      <T v="small" size={12.5} weight={selected ? '800' : '600'} color={selected ? C.white : C.ink2} lines={1}>{label}</T>
    </Press>
  );
}

export function Check({ checked, onPress, label }) {
  return (
    <Press onPress={onPress} style={styles.check} accessibilityLabel={label}>
      <View style={[styles.checkBox, checked && { backgroundColor: C.red, borderColor: C.red }]}>
        {checked ? <Ionicons name="checkmark" size={15} color={C.white} /> : null}
      </View>
      <T v="sub" color={C.ink2} style={{ marginLeft: 10 }}>{label}</T>
    </Press>
  );
}

/* Simple checks, the same ones the website's forms apply. */
export const valid = {
  email: (v) => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(String(v || '').trim()),
  filled: (v) => String(v || '').trim().length > 0,
};

const styles = StyleSheet.create({
  field: { marginBottom: 16 },
  box: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#DFE2E7',
    borderRadius: R.sm + 2,
    backgroundColor: C.field,
    overflow: 'hidden',
  },
  boxFocus: { borderColor: C.red, backgroundColor: C.white },
  boxError: { borderColor: C.red },
  input: { flex: 1, color: C.ink, paddingHorizontal: 14, paddingVertical: 10 },
  eye: { width: 44, height: 44, alignItems: 'center', justifyContent: 'center' },
  option: { flexDirection: 'row', alignItems: 'center', minHeight: 52, paddingHorizontal: 20, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: C.line2 },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    borderRadius: R.pill,
    borderWidth: 1,
    borderColor: C.line,
    backgroundColor: C.white,
    marginRight: 8,
    marginBottom: 8,
  },
  chipOn: { backgroundColor: C.red, borderColor: C.red },
  check: { flexDirection: 'row', alignItems: 'center', minHeight: 44 },
  checkBox: { width: 22, height: 22, borderRadius: 6, borderWidth: 1.5, borderColor: '#C9CDD3', alignItems: 'center', justifyContent: 'center' },
});
