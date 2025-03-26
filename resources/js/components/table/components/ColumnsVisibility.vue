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
import type { TableHeader } from '../index';
import DraggableList from '@/components/table/components/DraggableList.vue';
import { useToggleColumns } from '@/components/table/utils/toggleColumns';
import {computed} from 'vue';

interface Props {
    columns: TableHeader[]
    fixedColumns: string[]
}

const props = withDefaults(defineProps<Props>(), {
    fixedColumns: () => []
});

const { toggleColumn, updateLocalStorage, columns } = useToggleColumns(props.columns)

const getGhostParent = () => document.body

const onDropColumn = (items: TableHeader[]) => {
    updateLocalStorage([...nonDraggableColumns.value, ...items]);
}

const draggableColumns = computed(() => columns.value.filter((column: TableHeader) => !column.options.stickable))
const nonDraggableColumns = computed(() => columns.value.filter((column: TableHeader) => column.options.stickable))

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

            <DropdownMenuCheckboxItem v-for="item in nonDraggableColumns"
                                      :key="item.name"
                                      :checked="item.options.visible"
                                      @click="toggleColumn(item.name)"
                                      :disabled="fixedColumns.includes(item.name)"
            >
                {{item.header}}
            </DropdownMenuCheckboxItem>

            <DropdownMenuSeparator class="bg-gray-300" v-if="nonDraggableColumns.length > 0" />


            <DraggableList #default="{ item }: { item: TableHeader }"
                           :list="draggableColumns"
                           class="space-y-1"
                           :get-ghost-parent="getGhostParent"
                           @updated="onDropColumn"
            >
                <DropdownMenuCheckboxItem :key="item.name"
                                          :checked="item.options.visible"
                                          @click="toggleColumn(item.name)"
                                          :disabled="fixedColumns.includes(item.name)"
                >
                    {{item.header}}
                </DropdownMenuCheckboxItem>
            </DraggableList>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
