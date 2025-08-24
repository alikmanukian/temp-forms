import type { Paginated } from '@/components/table';
import { useTableStore } from '@/stores/table';

// public exposed functions
export const init = (props: Paginated<any>) => {
    const tableStore = useTableStore();
    tableStore.initializeTable({
        name: props.name,
        headers: props.headers,
        hash: props.hash,
        pageName: props.pageName,
        filters: props.filters
    });
};

export const useComponents = (name: string) => {
    const tableStore = useTableStore();

    const getColumns = () => tableStore.getColumns(name).value;
    const getFilteredColumns = tableStore.getFilteredColumns(name);

    const update = <K extends keyof import('@/stores/table').TableState>(
        property: K, 
        data: import('@/stores/table').TableState[K]
    ): void => {
        tableStore.updateTableProperty(name, property, data);
    };

    const getContainer = (): HTMLElement | null => 
        tableStore.getTableElement(name, 'container').value;
    
    const getTable = (): HTMLElement | null => 
        tableStore.getTableElement(name, 'table').value;
    
    const getScrollContainer = (): HTMLElement | null => 
        tableStore.getTableElement(name, 'scrollContainer').value;
    
    const getProperty = <K extends keyof import('@/stores/table').TableState>(
        property: K, 
        defaultValue: import('@/stores/table').TableState[K] | null = null
    ): import('@/stores/table').TableState[K] | null => {
        return tableStore.getTableProperty(name, property, defaultValue);
    };

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
