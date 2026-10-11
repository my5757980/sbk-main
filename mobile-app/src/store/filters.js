/* What the shop is showing: the search words, the filters, the order.
   Shared, so the home page's search, a brand tile or a body type can open the
   shop already filtered - the website does the same with ?brands[]=... */
import { createContext, useCallback, useContext, useMemo, useState } from 'react';

export const EMPTY = {
  search: '',
  brands: [],
  model: null,
  year: null,
  types: [],
  grades: [],
  maxMileage: null,
  transmission: null,
  minPrice: '',
  maxPrice: '',
  sort: 'default',
};

/** How many filters are on (the search box and the order are not filters). */
export function countActive(f) {
  return f.brands.length + f.types.length + f.grades.length
    + (f.model ? 1 : 0) + (f.year ? 1 : 0) + (f.maxMileage ? 1 : 0) + (f.transmission ? 1 : 0)
    + (f.minPrice !== '' && f.minPrice != null ? 1 : 0) + (f.maxPrice !== '' && f.maxPrice != null ? 1 : 0);
}

const FiltersContext = createContext(null);

export function FiltersProvider({ children }) {
  const [filters, setState] = useState(EMPTY);
  // Bumped whenever filters are set from outside the shop, so the shop can
  // scroll back to the top of a fresh result.
  const [stamp, setStamp] = useState(0);

  const setFilters = useCallback((next) => {
    setState({ ...EMPTY, ...next });
    setStamp((s) => s + 1);
  }, []);
  const patch = useCallback((part) => setState((f) => ({ ...f, ...part })), []);
  const reset = useCallback(() => setState((f) => ({ ...EMPTY, search: f.search, sort: f.sort })), []);

  const value = useMemo(() => ({ filters, stamp, setFilters, patch, reset }), [filters, stamp, setFilters, patch, reset]);
  return <FiltersContext.Provider value={value}>{children}</FiltersContext.Provider>;
}

export function useFilters() {
  return useContext(FiltersContext);
}
