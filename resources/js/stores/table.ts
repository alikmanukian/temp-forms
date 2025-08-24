import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import type { VisitOptions } from '@inertiajs/core';
import type { TableHeader, Filter, Clause } from '@/components/table';
import { buildData } from '@/components/table/utils/helpers';
import { processSearchData, type SearchParams } from '@/components/table/utils/filterable';

export interface TableState {
    name: string;
    columns: TableHeader[];
    hash: string;
    containerWidth: number;
    scrollSize: number;
    scrollPosition: number;
    showScrollButton: boolean;
    filters: Record<string, Filter>;
    searchString: string;
    loading: boolean;
    pageName: string;
}

const STORAGE_KEY_PREFIX = 'table_columns:';

export const useTableStore = defineStore('table', () => {
    // State
    const tables = ref<Record<string, TableState>>({});
    
    // Helper functions
    const getStorageKey = (name: string): string => 
        `${STORAGE_KEY_PREFIX}${window.location.pathname}:${name}`;
    
    const loadFromLocalStorage = (key: string): Partial<TableState> | null => {
        try {
            const stored = localStorage.getItem(key);
            return stored ? JSON.parse(stored) : null;
        } catch {
            return null;
        }
    };
    
    const saveToLocalStorage = (key: string, data: TableState): void => {
        try {
            localStorage.setItem(key, JSON.stringify(data));
        } catch {
            // Handle localStorage quota exceeded or other errors silently
        }
    };
    
    // Actions
    const initializeTable = (props: { 
        name: string; 
        headers: TableHeader[]; 
        hash: string;
        pageName: string;
        filters?: Filter[];
    }): void => {
        const key = getStorageKey(props.name);
        const page = usePage<{ query: any }>();
        
        // Initialize filters
        const initialFilters: Record<string, Filter> = {};
        if (props.filters) {
            props.filters.forEach((filter: Filter) => {
                initialFilters[filter.name] = { ...filter };
            });
        }
        
        // Get initial search from query string
        const getInitialSearch = (token: string) => {
            const filterParam = getFilterParam(props.pageName, token);
            return filterParam
                .split('.')
                .reduce((acc: any, key: string) => {
                    if (acc && acc.hasOwnProperty(key)) {
                        return acc[key];
                    }
                    return null;
                }, page.props.query);
        };
        
        const defaultState: TableState = {
            name: props.name,
            columns: props.headers,
            hash: props.hash,
            containerWidth: 0,
            scrollSize: 0,
            scrollPosition: 0,
            showScrollButton: true,
            filters: initialFilters,
            searchString: getInitialSearch('search') || '',
            loading: false,
            pageName: props.pageName,
        };
        
        // Try to load from localStorage
        const storedState = loadFromLocalStorage(key);
        
        if (storedState && storedState.hash === props.hash) {
            // Use stored state if hash matches, but update filters and search from URL
            const mergedState: TableState = {
                ...defaultState,
                ...storedState,
                filters: initialFilters, // Always use fresh filters from server
                searchString: getInitialSearch('search') || '', // Always use fresh search from URL
                pageName: props.pageName,
                loading: false, // Always start with loading false
            };
            tables.value[key] = mergedState;
            saveToLocalStorage(key, mergedState);
        } else {
            // Use default state and save it
            tables.value[key] = defaultState;
            saveToLocalStorage(key, defaultState);
        }
    };
    
    const updateTableProperty = <K extends keyof TableState>(
        name: string,
        property: K,
        value: TableState[K]
    ): void => {
        const key = getStorageKey(name);
        const table = tables.value[key];
        
        if (table) {
            table[property] = value;
            saveToLocalStorage(key, table);
        }
    };
    
    const getTable = (name: string): TableState | null => {
        const key = getStorageKey(name);
        return tables.value[key] || null;
    };
    
    const getTableProperty = <K extends keyof TableState>(
        name: string,
        property: K,
        defaultValue: TableState[K] | null = null
    ): TableState[K] | null => {
        const table = getTable(name);
        if (!table) return defaultValue;
        return table[property] ?? defaultValue;
    };
    
    // Computed getters
    const getColumns = (name: string) => computed(() => {
        const table = getTable(name);
        return table?.columns || [];
    });
    
    const getFilteredColumns = (name: string) => computed(() => {
        const columns = getColumns(name).value;
        return columns.filter(column => 
            column.visible === undefined || column.visible
        );
    });
    
    const getTableElement = (name: string, selector: string) => computed(() => {
        const containerSelector = `[data-name="table-container-${name}"]`;
        const container = document.querySelector(containerSelector);
        
        switch (selector) {
            case 'container':
                return container as HTMLElement | null;
            case 'table':
                return container?.querySelector('table') as HTMLElement | null;
            case 'scrollContainer':
                return container?.querySelector('table')?.closest('[data-name="scroll-container"]') as HTMLElement | null;
            default:
                return null;
        }
    });
    
    // Filter and pagination helper functions
    const getFilterParam = (pageName: string, token: string): string => {
        let filterParam = 'filter';

        if (pageName.includes('.')) {
            filterParam += '.' + pageName.split('.')[1];
        }

        filterParam += '.' + token;
        return filterParam;
    };
    
    const setSearchParams = (pageName: string, token: string, value: any) => {
        return {
            ...buildData(getFilterParam(pageName, token), value),
            ...buildData(pageName, 1),
        };
    };
    
    const deepMerge = (target: any, source: any) => {
        for (const key in source) {
            if (
                source[key] instanceof Object &&
                key in target &&
                target[key] instanceof Object
            ) {
                deepMerge(target[key], source[key]);
            } else {
                target[key] = structuredClone(source[key]);
            }
        }
        return target;
    };
    
    // Request/reload action
    const reload = (name: string, searchData: SearchParams, options: VisitOptions = {}) => {
        const table = getTable(name);
        if (!table) return;
        
        updateTableProperty(name, 'loading', true);
        
        let params: VisitOptions = {
            only: ['query', name],
            onStart: () => updateTableProperty(name, 'loading', true),
            onFinish: () => updateTableProperty(name, 'loading', false),
            preserveState: true,
            preserveScroll: true,
        };

        params = {...params, ...options};

        // Get current query parameters
        const urlSearchParams = new URLSearchParams(window.location.search);
        const currentParams: Record<string, any> = {};

        // Parse all current URL parameters
        for (const [key, value] of urlSearchParams.entries()) {
            if (key.includes('[') && key.includes(']')) {
                const parts = key.replace(/\]/g, '').split('[');
                let current = currentParams;

                for (let i = 0; i < parts.length - 1; i++) {
                    if (!current[parts[i]]) {
                        current[parts[i]] = {};
                    }
                    current = current[parts[i]];
                }

                current[parts[parts.length - 1]] = value;
            } else {
                currentParams[key] = value;
            }
        }

        const processedData = processSearchData(searchData, currentParams);

        // Remove page if it's 1 (default page)
        if (processedData.page === 1) {
            delete processedData.page;
        }

        // Handle nested page parameters
        if (processedData.page && typeof processedData.page === 'object') {
            Object.entries(processedData.page).forEach(([key, value]) => {
                if (value === 1) {
                    delete (processedData.page as Record<string, number>)[key];
                }
            });

            if (Object.keys(processedData.page).length === 0) {
                delete processedData.page;
            }
        }

        params.data = processedData;
        router.visit(window.location.pathname, params);
    };
    
    // Filter actions
    const searchBy = (tableName: string, field: string, value: string | string[], clause: string | null, callback?: (filter?: Filter) => void) => {
        const table = getTable(tableName);
        if (!table) return;
        
        // Normalize value to string
        let normalizedValue = Array.isArray(value) ? value.join(',') : (value ?? '');

        // Add clause prefix if provided
        if (clause && normalizedValue) {
            normalizedValue = `${clause}.${normalizedValue}`;
        } else if (clause) {
            normalizedValue = clause;
        }

        const searchParams = setSearchParams(table.pageName, field, normalizedValue);
        
        reload(tableName, searchParams, {
            onSuccess: (response: any) => {
                const responseFilters = response.props[tableName]?.filters;
                if (responseFilters) {
                    const filter = responseFilters.find((filter: Filter) => filter.name === field);
                    callback?.(filter);

                    if (filter) {
                        updateTableProperty(tableName, 'filters', {
                            ...table.filters,
                            [field]: filter
                        });
                    }
                }
            }
        });
    };
    
    const updateFilter = (tableName: string, field: string, value: string | string[], clause: Clause | null) => {
        searchBy(tableName, field, value, clause?.searchSymbol ?? null, (filter?: Filter) => {
            if (filter) {
                filter.selected = true;
                filter.selectedClause = clause;
            }
        });
    };
    
    const deleteFilter = (tableName: string, field: string) => {
        searchBy(tableName, field, '', null);
    };
    
    const addFilter = (tableName: string, name: string) => {
        const table = getTable(tableName);
        if (!table) return;
        
        updateTableProperty(tableName, 'filters', {
            ...table.filters,
            [name]: {
                ...table.filters[name],
                selected: true,
                opened: true,
                selectedClause: null,
            }
        });
    };
    
    const resetFilters = (tableName: string) => {
        const table = getTable(tableName);
        if (!table) return;
        
        let searchParams: Record<string, any> = {};

        // Set empty filters
        Object.keys(table.filters).forEach(key => {
            searchParams = deepMerge(searchParams, setSearchParams(table.pageName, key, ''));
        });

        // Set empty global search string
        searchParams = deepMerge(searchParams, setSearchParams(table.pageName, 'search', ''));

        reload(tableName, searchParams, {
            onSuccess: () => {
                // Clear filters
                const updatedFilters = { ...table.filters };
                Object.keys(updatedFilters).forEach(key => {
                    updatedFilters[key].selected = false;
                    updatedFilters[key].value = null;
                });
                
                updateTableProperty(tableName, 'filters', updatedFilters);
                updateTableProperty(tableName, 'searchString', '');
            }
        });
    };
    
    const updateSearchString = (tableName: string, value: string) => {
        updateTableProperty(tableName, 'searchString', value);
    };
    
    // Pagination action
    const changePage = (tableName: string, page: number) => {
        const table = getTable(tableName);
        if (!table) return;
        
        reload(tableName, buildData(table.pageName, page));
    };

    // Cleanup function
    const clearTable = (name: string): void => {
        const key = getStorageKey(name);
        delete tables.value[key];
        localStorage.removeItem(key);
    };
    
    // Computed getters for filters
    const getFilters = (name: string) => computed(() => {
        const table = getTable(name);
        return table?.filters || {};
    });
    
    const getSearchString = (name: string) => computed(() => {
        const table = getTable(name);
        return table?.searchString || '';
    });
    
    const getLoading = (name: string) => computed(() => {
        const table = getTable(name);
        return table?.loading || false;
    });
    
    const showFiltersRowInHeader = (name: string, initialFilters: Filter[]) => computed(() => {
        return initialFilters.some((filter: Filter) => filter.showInHeader);
    });
    
    const filtersAreApplied = (name: string) => computed(() => {
        const table = getTable(name);
        if (!table) return false;
        
        const hasSelectedFilters = Object.entries(table.filters)
            .filter(([_, config]) => config && config.selected).length > 0;
        const hasSearchString = table.searchString?.length > 0;
        
        return hasSelectedFilters || hasSearchString;
    });

    return {
        // State
        tables: computed(() => tables.value),
        
        // Actions  
        initializeTable,
        updateTableProperty,
        getTable,
        getTableProperty,
        clearTable,
        
        // Request actions
        reload,
        
        // Filter actions
        searchBy,
        updateFilter,
        deleteFilter,
        addFilter,
        resetFilters,
        updateSearchString,
        
        // Pagination actions
        changePage,
        
        // Computed getters
        getColumns,
        getFilteredColumns,
        getTableElement,
        getFilters,
        getSearchString,
        getLoading,
        showFiltersRowInHeader,
        filtersAreApplied,
    };
});