<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';

import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useLocalStorage } from '@vueuse/core';
import { type TableHeader, TableHeaderWithoutOptions } from '@/types';
import DraggableList from '@/components/table/components/DraggableList.vue';
import { computed, ref } from 'vue';

interface Props {
    columns: TableHeader[]
    fixedColumns: string[]
}

const props = withDefaults(defineProps<Props>(), {
    fixedColumns: () => []
});

const sortArr1ByArr2 = (arr1: TableHeaderWithoutOptions[], arr2: TableHeaderWithoutOptions[]) => {
    arr1.sort((a, b) => {
        const indexA = arr2.findIndex(obj => obj.name === a.name);
        const indexB = arr2.findIndex(obj => obj.name === b.name);
        return (indexA === -1 ? Infinity : indexA) - (indexB === -1 ? Infinity : indexB);
    })
}

const columnNames = (columns: TableHeaderWithoutOptions[]) => columns.map((column) => column.name)

function arrayDifference(arr1: string[], arr2: string[]) {
    const set2 = new Set(arr2); // Convert arr2 to a Set for fast lookup
    return arr1.filter(item => !set2.has(item)); // O(n) instead of O(n²)
}

const columns = ref<TableHeader[]>(props.columns)

const initialColumns = columns.value.map((column: TableHeader) => ({
    name: column.name, header: column.header, width: column.width
}))

const filteredColumns = useLocalStorage('table_columns:' + window.location.pathname, initialColumns)
sortArr1ByArr2(columns.value, filteredColumns.value)


const toggleColumn = (columnName: string) => {
    const hiddenColumns = arrayDifference(columnNames(columns.value), columnNames(filteredColumns.value));

    filteredColumns.value = columns.value.filter((column) => {
        const includes = hiddenColumns.includes(column.name);
        return column.name === columnName ? includes : !includes;
    }).map((column: TableHeader) => ({
        name: column.name, header: column.header, width: column.width
    }));
}

const getGhostParent = () => document.body

const onDropColumn = (items: TableHeader[]) => {
    columns.value = items
    sortArr1ByArr2(filteredColumns.value, items);
}
</script>

<template>
    <DropdownMenu class="relative">
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="flex items-center">
                <Icon name="Columns3" class="mr-1 w-4 h-4"/>
                <span class="text-sm">Columns</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
            <DropdownMenuSeparator class="bg-gray-300" />

            <DraggableList #default="{ item }: { item: TableHeader }"
                           :list="columns"
                           class="space-y-1"
                           :get-ghost-parent="getGhostParent"
                           @updated="onDropColumn"
            >
                <DropdownMenuCheckboxItem :key="item.name"
                                          :checked="filteredColumns.map((column) => column.name).includes(item.name)"
                                          @click="toggleColumn(item.name)"
                                          :disabled="fixedColumns.includes(item.name)"
                >
                    {{item.header}}
                </DropdownMenuCheckboxItem>
            </DraggableList>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
