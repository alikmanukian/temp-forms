import { ref, computed } from 'vue';
import type { TableHeader } from '@/components/table';
import { useLocalStorage } from '@vueuse/core';
import { useScrollable } from './scrollable'

interface Props {
    name: string
    columns: TableHeader[]
    container: HTMLElement|null
    scrollContainer: HTMLElement|null
    table: HTMLElement|null
    containerWidth: number
    scrollSize: number
}

const tables = ref<Record<string, Props>>({});

// private function s
const localStorageKey = (name: string): string => 'table_columns:' + window.location.pathname + ':' + name;

const localStorage = (pageName: string): Props => tables.value[localStorageKey(pageName)]

// public exposed functions
export const init = ({ pageName, columns, container }: {
    pageName: string,
    columns: TableHeader[],
    container: HTMLElement | null,
}) => {
    const key = localStorageKey(pageName);
    const tableElement = container?.querySelector('table') || null
    const columnsRef = useLocalStorage<TableHeader[]>(key, columns)

    tables.value[key] = {
        name: pageName,
        container: container,
        table: tableElement,
        scrollContainer: tableElement?.closest('[data-name="scroll-container"]') || null,
        containerWidth: container?.getBoundingClientRect().width || 0,
        columns: columnsRef.value,
        scrollSize: container?.scrollWidth || 0
    };

    const {calculateScrollSize} = useScrollable(pageName)

    calculateScrollSize();
};

export const useComponents = (pageName: string) => {
    const getColumns = (): TableHeader[] => localStorage(pageName)?.columns || [];

    const getFilteredColumns = computed<TableHeader[]>(() => {
        console.log('getFilteredColumns');
        const columns = getColumns();
        return columns.filter((column: TableHeader) => column.options.visible);
    });

    const updateColumns = (pageName: string, updatedColumns: TableHeader[]): void => {
        const key = localStorageKey(pageName);
        const table = localStorage(pageName);

        if (table) {
            const columns = useLocalStorage<TableHeader[]>(key, table.columns);
            columns.value = updatedColumns;
            table.columns = columns.value;
        }
    };

    const getContainer = (pageName: string): HTMLElement|null => localStorage(pageName)?.container ?? null
    const getTable = (pageName: string): HTMLElement|null => localStorage(pageName)?.table ?? null
    const getScrollContainer = (pageName: string): HTMLElement|null => localStorage(pageName)?.scrollContainer ?? null

    return {
        getColumns,
        getFilteredColumns,
        getContainer,
        getTable,
        getScrollContainer,
        updateColumns,
    };
};
