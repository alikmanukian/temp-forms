<script lang="ts" setup>
import { type Paginated } from '@/types';
import Pagination from '@/components/table/components/Pagination.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow
} from '@/components/ui/table';
import RowsPerPage from '@/components/table/components/RowsPerPage.vue';
import { computed } from 'vue';
import ColumnsVisibility from '@/components/table/components/ColumnsVisibility.vue';
import { useLocalStorage } from '@vueuse/core';

interface Props {
    resource: Paginated<any>;
    reloadOnly: boolean | string[];
    stickyPagination: boolean;
    stickyHeader: boolean;
    includeQueryString: boolean;
    fixedColumn: string;
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    stickyHeader: false,
    stickyPagination: false,
    includeQueryString: true,
    fixedColumn: 'id'
});

const from = computed(() => (props.resource.meta.currentPage - 1) * props.resource.meta.perPage + 1);
const to = computed(() => props.resource.meta.currentPage * props.resource.meta.perPage);
const noResults = computed(() => props.resource.data.length === 0)

const filteredColumns = useLocalStorage('table_columns:' + window.location.pathname, props.resource.headers)

const onTableResizeStart = (e: CustomEvent) => {
    const el = e.target as HTMLElement

    const table = el.closest('table') as HTMLElement

    table.querySelectorAll('colgroup col').forEach((col: HTMLElement) => col.style.width = 'auto')
}
const onTableResizeEnd = (e: CustomEvent) => {
    const el = e.target as HTMLElement
    el.querySelectorAll('th').forEach((th: HTMLElement, index: number) => filteredColumns.value[index].width = th.style.width)
}
</script>

<template>
    <div>
        <div class="flex justify-between items-center">
            <div class="flex-1 text-sm font-medium" v-if="!noResults">
                Showing <span class="font-bold">{{ from }}-{{ to }}</span> of <span class="font-bold">{{ resource.meta.total }}</span> records
            </div>

            <div class="ml-auto flex items-center gap-4">
                <RowsPerPage :meta="resource.meta"
                             :reloadOnly
                             :includeQueryString
                             :pageName="resource.pageName"
                />

                <ColumnsVisibility :columns="resource.headers" :fixedColumn />
            </div>
        </div>

        <Table v-if="!noResults" border="1">
            <colgroup>
                <col :style="{width: column.width}" v-for="column in filteredColumns" :key="column.name">
            </colgroup>

            <TableHeader :class="{'sticky top-0 bg-opacity-75 backdrop-blur backdrop-filter': stickyHeader}"
                         v-columns-resizable
                         @resize:start="onTableResizeStart"
                         @resize:end="onTableResizeEnd"
            >
                <TableRow>
                    <TableHead v-for="column in filteredColumns" :key="column.name">{{ column.header }}</TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <TableRow v-for="(row, index) in resource.data" :key="index" style="white-space: nowrap">
                    <TableCell v-for="column in filteredColumns" :key="column.name">
                        {{ row[column.name] }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <div v-else class="flex flex-col items-center justify-center min-h-60 gap-4">
            <img src="../images/no-results.svg" class="w-[200px]" />
            <span class="text-lg">No results found</span>
        </div>

        <Pagination :meta="resource.meta"
                    :pageName="resource.pageName"
                    :reloadOnly
                    :stickyPagination
                    :includeQueryString
        ></Pagination>
    </div>
</template>
