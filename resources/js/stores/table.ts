import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { TableHeader } from '@/components/table';

export interface TableState {
    name: string;
    columns: TableHeader[];
    hash: string;
    containerWidth: number;
    scrollSize: number;
    scrollPosition: number;
    showScrollButton: boolean;
}

const STORAGE_KEY_PREFIX = 'table_columns:';

export const useTableStore = defineStore('table', () => {
    // State
    const tables = ref<Record<string, TableState>>({});
    
    // Helper functions
    const getStorageKey = (name: string): string => 
        `${STORAGE_KEY_PREFIX}${window.location.pathname}:${name}`;
    
    const loadFromLocalStorage = (key: string): TableState | null => {
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
    }): void => {
        const key = getStorageKey(props.name);
        
        const defaultState: TableState = {
            name: props.name,
            columns: props.headers,
            hash: props.hash,
            containerWidth: 0,
            scrollSize: 0,
            scrollPosition: 0,
            showScrollButton: true,
        };
        
        // Try to load from localStorage
        const storedState = loadFromLocalStorage(key);
        
        if (storedState && storedState.hash === props.hash) {
            // Use stored state if hash matches
            tables.value[key] = storedState;
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
    
    // Cleanup function
    const clearTable = (name: string): void => {
        const key = getStorageKey(name);
        delete tables.value[key];
        localStorage.removeItem(key);
    };
    
    return {
        // State
        tables: computed(() => tables.value),
        
        // Actions  
        initializeTable,
        updateTableProperty,
        getTable,
        getTableProperty,
        clearTable,
        
        // Computed getters
        getColumns,
        getFilteredColumns,
        getTableElement,
    };
});