import { ref } from 'vue';
import type { TableHeader } from '@/components/table';
import { RemovableRef, useLocalStorage } from '@vueuse/core';

interface Props {
    name: string
    columns: RemovableRef<TableHeader[]>
    container: HTMLElement|null
    scrollContainer: HTMLElement|null
    table: HTMLElement|null
    containerWidth: number
    scrollSize: number
}

const tables = ref<Record<string, Props>>({});

export const getLocalStorageKey = (name: string) => {
    return 'table_columns:' + window.location.pathname + ':' + name;
};

export const filteredColumns = (pageName: string) => {
    const columns = getColumns(pageName) as TableHeader[];
    return columns?.filter((column: TableHeader) => column.options.visible);
};

export const init = ({ pageName, columns, container }: {
    pageName: string,
    columns: TableHeader[],
    container: HTMLElement | null,
}) => {
    const tableElement = container?.querySelector('table') || null

    tables.value[getLocalStorageKey(pageName)] = {
        name: pageName,
        container: container,
        table: tableElement,
        scrollContainer: tableElement?.closest('[data-name="scroll-container"]') || null,
        containerWidth: container?.getBoundingClientRect().width || 0,
        columns: useLocalStorage<TableHeader[]>(getLocalStorageKey(pageName), columns),
        scrollSize: container?.scrollWidth || 0
    };
};

export const getTable = (pageName: string): Props|null => tables.value[getLocalStorageKey(pageName)]

export const getColumns = (pageName: string): RemovableRef<TableHeader[]> | TableHeader[] => {
    const table = getTable(pageName);
    if (!table) {
        return [];
    }
    return table.columns;
};

export const updateColumns = (pageName: string, updatedColumns: TableHeader[]): void => {
    const key = getLocalStorageKey(pageName);
    const table = getTable(pageName);

    if (table) {
        const columns = useLocalStorage<TableHeader[]>(key, table.columns);
        columns.value = updatedColumns;
        table.columns = columns;
    }
};

export const useComponents = () => {
    return {
        columns: getColumns,
        filteredColumns,
        updateColumns
    };
};
