import { ref, computed, Ref } from 'vue';
import type { TableHeader } from '@/components/table';
import { useLocalStorage } from '@vueuse/core';
import { useScrollable } from './scrollable'

interface ColumnPosition {
    left: number
    width: number
}

interface Table {
    name: string
    columns: TableHeader[]
    container: HTMLElement|null
    columnsPositions: Ref<Record<string, ColumnPosition>>
    containerWidth: Ref<number, number>
    scrollSize: Ref<number, number>
    scrollPosition: Ref<number, number>
    showScrollButton: Ref<boolean, boolean>
}

const tables = ref<Record<string, Table>>({});

// private function s
const localStorageKey = (name: string): string => 'table_columns:' + window.location.pathname + ':' + name;

const localStorage = (pageName: string): Table => tables.value[localStorageKey(pageName)]

// public exposed functions
export const init = ({ pageName, columns, container }: {
    pageName: string,
    columns: TableHeader[],
    container: HTMLElement | null,
}) => {
    console.log('init');
    const key = localStorageKey(pageName);
    const columnsRef = useLocalStorage<TableHeader[]>(key, columns)

    tables.value[key] = {
        name: pageName,
        container: container,
        columns: columnsRef.value,
        columnsPositions: ref<Record<string, ColumnPosition>>({}),
        containerWidth: ref(0),
        scrollSize: ref(0),
        scrollPosition: ref(0),
        showScrollButton: ref(true),
    };

    const {updateScrollSize, saveColumnsPositions, updateContainerWidth} = useScrollable(pageName)

    setTimeout(() => {
        updateContainerWidth();
        updateScrollSize();
        saveColumnsPositions();
    }, 0)
};

export const useComponents = (pageName: string) => {
    const getColumns = (): TableHeader[] => localStorage(pageName)?.columns || [];

    const getFilteredColumns = computed<TableHeader[]>(() => {
        const columns = getColumns();
        return columns.filter((column: TableHeader) => column.options.visible);
    });

    const updateColumns = (updatedColumns: TableHeader[]): void => {
        const key = localStorageKey(pageName);
        const table = localStorage(pageName);

        if (table) {
            const columns = useLocalStorage<TableHeader[]>(key, table.columns);
            columns.value = updatedColumns;
            table.columns = columns.value;
        }
    };

    const getContainer = (): HTMLElement|null => localStorage(pageName)?.container ?? null
    const getTable = (): HTMLElement|null => getContainer()?.querySelector('table') || null
    const getScrollContainer = (): HTMLElement|null => getTable()?.closest('[data-name="scroll-container"]') || null
    const getContainerWidth = (): Ref<number, number> => localStorage(pageName)?.containerWidth || ref(0)

    return {
        getColumns,
        getFilteredColumns,
        getContainer,
        getTable,
        getScrollContainer,
        getContainerWidth,
        updateColumns,
    };
};
