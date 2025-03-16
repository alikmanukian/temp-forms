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

interface Props {
    columns: Record<string, string>
    fixedColumn: keyof Props["columns"]
}

const props = withDefaults(defineProps<Props>(), {
    fixedColumn: 'id'
});

const filteredColumns = useLocalStorage('table_columns:' + window.location.pathname, props.columns)

const toggleColumn = (columnName: string) => {
    const exists = filteredColumns.value.hasOwnProperty(columnName);
    console.log(exists);
    if (exists) {
        filteredColumns.value = Object.fromEntries(
            Object.entries(filteredColumns.value).filter(([name, _]) => name !== columnName)
        )
    } else {
        filteredColumns.value = Object.fromEntries(
            Object.entries(props.columns).filter(([name, _]) => filteredColumns.value.hasOwnProperty(name) || name === columnName)
        )
    }
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="flex items-center">
                <Icon name="Columns3" class="mr-1 w-4 h-4"/>
                <span class="text-sm">Columns</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
            <DropdownMenuSeparator class="bg-gray-300" />
            <DropdownMenuCheckboxItem v-for="(header, name) in columns"
                                      :key="name"
                                      :checked="filteredColumns.hasOwnProperty(name)"
                                      @click="toggleColumn(name)"
                                      :disabled="name === fixedColumn"
            >
                {{header}}
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
