import { useLocalStorage } from '@vueuse/core';
import type { TableHeader } from '@/components/table';
import { computed, ref } from 'vue';

const sortArr1ByArr2 = (arr1: TableHeader[], arr2: TableHeader[]) => {
    arr1.sort((a, b) => {
        const indexA = arr2.findIndex(obj => obj.name === a.name);
        const indexB = arr2.findIndex(obj => obj.name === b.name);
        return (indexA === -1 ? Infinity : indexA) - (indexB === -1 ? Infinity : indexB);
    })
}

export const useToggleColumns = (initialColumns: TableHeader[]) => {
    const columns = ref<TableHeader[]>(initialColumns)
    const localStorage = useLocalStorage<TableHeader[]>('table_columns:' + window.location.pathname, columns)
    const filteredColumns = computed(() => columns.value.filter((column: TableHeader) => column.options.visible))

    const toggleColumn = (columnName: string) => {
        const hiddenColumns = columns.value
            .filter((column: TableHeader) => !column.options.visible)
            .map((column: TableHeader) => column.name)

        localStorage.value = columns.value.map((column: TableHeader) => {
            const includes = hiddenColumns.includes(column.name);
            column.options.visible = column.name === columnName ? includes : !includes;
            return column;
        })
    }

    const updateLocalStorage = (updatedColumns: TableHeader[]) => {
        columns.value = updatedColumns
        sortArr1ByArr2(localStorage.value, updatedColumns)
    }

    return {
        columns,
        filteredColumns,
        localStorage,
        toggleColumn,
        updateLocalStorage
    };
}

