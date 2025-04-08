import { computed, reactive } from 'vue';
import type { Paginated, TableHeader } from '@/components/table';
import { useLocalStorage } from '@vueuse/core';

interface Table {
    name: string
    columns: TableHeader[]
    hash: string
    containerWidth: number
    scrollSize: number
    scrollPosition: number
    showScrollButton: boolean
}

const tables = reactive<Record<string, Table>>({});

// private function s
const localStorageKey = (name: string): string => 'table_columns:' + window.location.pathname + ':' + name;

const fromLocalStorage = (pageName: string): Table|null => {
    return tables[localStorageKey(pageName)]
}

// public exposed functions
export const init = (props: Paginated<any>) => {
    const key = localStorageKey(props.pageName);

    const tableData: Table = {
        name: props.pageName,
        columns: props.headers,
        hash: props.hash,
        containerWidth: 0,
        scrollSize: 0,
        scrollPosition: 0,
        showScrollButton: true,
    };

    const localStorageData = useLocalStorage<Table>(key, tableData)

    if (localStorageData.value.hash !== props.hash) {
        localStorageData.value = tableData;
    }

    tables[key] = localStorageData.value;
};

export const useComponents = (pageName: string) => {
    const getColumns = (): TableHeader[] => fromLocalStorage(pageName)?.columns || [];

    const getFilteredColumns = computed<TableHeader[]>(() => {
        const columns = getColumns();
        return columns.filter((column: TableHeader) => {
            return column.visible === undefined || column.visible
        });
    });

    const update = (property: keyof Table, data: any): void => {
        const table = fromLocalStorage(pageName);

        if (table) {
            const storage = useLocalStorage<Table>(localStorageKey(pageName), table)
            storage.value = {...table, [property]: data}
            table[property] = data as never
        }
    };

    const getContainer = (): HTMLElement|null => document.querySelector(`[data-name="table-container-${pageName}"]`) ?? null
    const getTable = (): HTMLElement|null => getContainer()?.querySelector('table') || null
    const getScrollContainer = (): HTMLElement|null => getTable()?.closest('[data-name="scroll-container"]') || null
    const getProperty = (property: keyof Table, defaultValue: any|null = null): any => {
        const storage = fromLocalStorage(pageName);
        if (!storage) {
            return null;
        }
        return storage[property] || defaultValue;
    }

    return {
        getColumns,
        getFilteredColumns,
        getContainer,
        getTable,
        getScrollContainer,
        getProperty,
        update,
    };
};
