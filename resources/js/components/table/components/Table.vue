<script lang="ts" setup>
import { type Paginated } from '@/types';
import Pagination from '@/components/table/components/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed } from 'vue';
import { useLocalStorage } from '@vueuse/core';
import vResizable from '../utils/resizable';
import EmptyState from '@/components/table/components/EmptyState.vue';
import ToolsRow from '@/components/table/components/ToolsRow.vue';

interface Props {
    resource: Paginated<any>;
    reloadOnly?: boolean | string[];
    stickyPagination?: boolean;
    stickyHeader?: boolean;
    includeQueryString?: boolean;
    fixedColumns?: string[];
    resizable?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    stickyHeader: false,
    stickyPagination: false,
    includeQueryString: true,
    resizable: false,
    fixedColumns: () => ['id'],
});

const noResults = computed(() => props.resource.data.length === 0);

const filteredColumns = useLocalStorage('table_columns:' + window.location.pathname, props.resource.headers);

const onTableResizeStart = (e: CustomEvent) => {
    const el = e.target as HTMLElement;

    el.querySelectorAll('colgroup col').forEach((col: HTMLElement) => (col.style.width = 'auto'));
};
const onTableResizeEnd = (e: CustomEvent) => {
    const el = e.target as HTMLElement;
    el.querySelectorAll('th').forEach((th: HTMLElement, index: number) => (filteredColumns.value[index].width = th.style.width));
};
</script>

<template>
    <div class="-mx-4">
        <ToolsRow v-if="!noResults" :meta="resource.meta" :page-name="resource.pageName" :headers="resource.headers" :reloadOnly :fixedColumns />

        <Table
            v-if="!noResults"
            v-resizable="resizable"
            @resize:start="onTableResizeStart"
            @resize:end="onTableResizeEnd"
            :style="{ overflow: stickyHeader ? 'visible' : 'auto' }"
            class="table-fixed"
        >
            <colgroup v-if="!resizable">
                <col :style="{ width: column.width }" v-for="column in filteredColumns" :key="column.name" />
            </colgroup>

            <TableHeader
                class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide dark:bg-black/25"
                :class="{ 'sticky top-0 bg-opacity-75 shadow-md shadow-gray-300/25 backdrop-blur backdrop-filter': stickyHeader }"
            >
                <TableRow>
                    <TableHead v-for="column in filteredColumns" :style="{ width: column.width }" class="dark:text-white" :key="column.name">
                        <div class="flex items-center justify-start">
                            <div class="truncate">
                                {{ column.header }}
                            </div>
                        </div>
                    </TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <TableRow v-for="(row, index) in resource.data" :key="index">
                    <TableCell v-for="column in filteredColumns" :key="column.name">
                        <div class="flex items-center justify-start">
                            <div class="truncate">{{ row[column.name] }}</div>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>

        <EmptyState v-else />

        <Pagination
            :meta="resource.meta"
            :pageName="resource.pageName"
            :reloadOnly
            :stickyPagination
            :includeQueryString
            hide-page-numbers
        ></Pagination>
    </div>
</template>
