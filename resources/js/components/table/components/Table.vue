<script lang="ts" setup>
import FiltersButton from '@/components/table/components/FiltersButton.vue';
import FiltersRow from '@/components/table/components/FiltersRow.vue';
import SearchInput from '@/components/table/components/SearchInput.vue';
import { useFilters } from '@/components/table/utils/filterable';
import { buildData, columnWrappingMethod } from '@/components/table/utils/helpers';
import { useRequest } from '@/components/table/utils/request';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { cn } from '@/lib/utils';
import { computed, nextTick, onMounted, onUnmounted, provide, useTemplateRef } from 'vue';
import EmptyState from '../components/EmptyState.vue';
import HeaderButton from '../components/HeaderButton.vue';
import Pagination from '../components/Pagination.vue';
import ScrollToRight from './ScrollToRight.vue';
import ToolsRow from '../components/ToolsRow.vue';
import type { Paginated, TableHeader as TableHeaderType } from '../index';
import { init, useComponents } from '../utils/components';
import vResizable from '../utils/resizable';
import { useScrollable } from '../utils/scrollable';
import { useResizeObserver } from '../utils/resize-observer';
import * as Columns from './columns';
import * as Filters from './filters/inputs';
import Loader from './Loader.vue';

// TYPES ---------------------------------------
interface Props {
    resource: Paginated<any>;
    expanded?: boolean;
    hidePageNumbers?: boolean;
}

type ColumnTypes = keyof typeof Columns;

// PROPS ---------------------------------------
const props = withDefaults(defineProps<Props>(), {
    expanded: false,
    hidePageNumbers: false,
});

const noResults = computed(() => props.resource.data.length === 0);
const name = props.resource.name;
const pageName = props.resource.pageName;

const { getFilteredColumns } = useComponents(name);
const { scrollPosition, scrollable, updateScrollPosition, updateScrollSize, updateContainerWidth, saveColumnsPositions } = useScrollable(name);

const { searchString, filters, showFiltersRowInHeader, filtersAreApplied, searchBy, onUpdateFilter, onDeleteFilter, onAddFilter, resetFilters } =
    useFilters(pageName, name, props.resource.filters);

const { reload, loading } = useRequest(props.resource.name);
const { observe, unobserve } = useResizeObserver();

const container = useTemplateRef<HTMLElement>('container');

// Memoize these computations to avoid unnecessary recalculations
const stickyHeader = computed(() => props.resource.stickyHeader);
const stickyPagination = computed(() => props.resource.stickyPagination);

// Memoized cell class computation with cached results
const cellClassCache = new Map<string, string>();
const cellClass = (column: TableHeaderType) => {
    const cacheKey = `${column.name}-${column.options.alignment}-${column.options.cellClass}-${stickyHeader.value}`;

    if (cellClassCache.has(cacheKey)) {
        return cellClassCache.get(cacheKey)!;
    }

    const result = cn([
        columnWrappingMethod(column, stickyHeader.value),
        column.options.alignment,
        column.options.cellClass
    ]);

    cellClassCache.set(cacheKey, result);
    return result;
};

// Memoized table element references to avoid repeated DOM queries
const tableElements = computed(() => {
    const { getContainer, getTable, getScrollContainer } = useComponents(name);
    return {
        container: getContainer(),
        table: getTable(),
        scrollContainer: getScrollContainer()
    };
});

// Optimized resize handler using global ResizeObserver
const handleResize = () => {
    updateContainerWidth();
    updateScrollSize();
    saveColumnsPositions();
};

onMounted(() => {
    init(props.resource);

    nextTick(() => {
        updateContainerWidth();
        updateScrollSize();
        saveColumnsPositions();

        if (container.value) {
            observe(container.value, handleResize, {
                id: `table-${name}`,
                delay: 16 // ~60fps
            });
        }
    });
});

onUnmounted(() => {
    if (container.value) {
        unobserve(container.value, `table-${name}`);
    }
    // Clear memoization cache to prevent memory leaks
    cellClassCache.clear();
});

provide('name', name);
provide('pageName', pageName);
provide('filters', filters);
provide('filtersAreApplied', filtersAreApplied);

const onPageChange = (page: number) => {
    reload(buildData(pageName, page));
};
</script>

<template>
    <div class="@container" :class="{ '-mx-4': expanded }" ref="container" :data-name="`table-container-${name}`">
        <!-- Search -->
        <div class="flex space-x-3 p-4">
            <SearchInput
                v-model="searchString"
                :searchable="resource.searchable"
                class="flex-1"
                @update="(value: string) => (searchString = value)"
                placeholder="Type to search ..."
                :search="searchBy"
            />

            <FiltersButton @update="onAddFilter" @reset="searchString = ''" :resetFilters="resetFilters" />
        </div>

        <FiltersRow @update="onUpdateFilter" @delete="onDeleteFilter" />

        <ToolsRow v-if="!noResults" :meta="resource.meta" :headers="resource.headers" :class="{ 'px-4': expanded }" @update="onPageChange" />

        <div v-if="!noResults" class="relative" :class="{ 'border-b border-t': expanded, 'rounded-lg border': !expanded }">
            <Table
                v-resizable="resource.resizable"
                @resize:end="saveColumnsPositions"
                :style="{ overflow: scrollable ? 'auto' : 'visible' }"
                @scroll="updateScrollPosition"
                :class="{ 'table-fixed': true, scrolled: scrollPosition > 0 }"
                data-name="scroll-container"
            >
                <colgroup v-if="!resource.resizable">
                    <col :style="{ width: column.width }" v-for="column in getFilteredColumns" :key="column.name" />
                </colgroup>

                <TableHeader :class="{ 'sticky top-0 z-10 shadow-sm shadow-gray-300/25': stickyHeader }">
                    <TableRow
                        class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide dark:bg-black/25 dark:text-white"
                        :class="{
                            'bg-opacity-75 backdrop-blur backdrop-filter': stickyHeader,
                            '!border-b-0': showFiltersRowInHeader,
                        }"
                    >
                        <TableHead
                            v-for="column in getFilteredColumns"
                            :style="{ width: column.width, left: column.sticked ? column.left + 'px' : '' }"
                            :key="column.name"
                            :data-name="column.name"
                            class="px-0"
                            :class="{
                                'sticky z-10 bg-gray-50/90 dark:bg-background/80': column.sticked,
                            }"
                        >
                            <HeaderButton :column />
                        </TableHead>
                    </TableRow>

                    <TableRow v-if="showFiltersRowInHeader" class="bg-white/90 dark:bg-background/80">
                        <TableHead
                            v-for="(column, index) in getFilteredColumns"
                            :style="{ width: column.width, left: column.sticked ? column.left + 'px' : '' }"
                            :key="column.name"
                            :data-name="column.name"
                            class="px-1 py-2"
                            :class="{
                                'sticky z-10': column.sticked,
                                'pl-2': index === 0,
                                'pr-2': index === getFilteredColumns.length - 1,
                            }"
                        >
                            <component
                                v-if="filters[column.name].showInHeader"
                                :modelValue="filters[column.name].value"
                                @update="(value: string | string[], clause: string | null) => searchBy(column.name, value, clause)"
                                :is="Filters[filters[column.name].component as keyof typeof Filters]"
                                :filter="filters[column.name]"
                                :inHeader="true"
                            ></component>
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="(row, index) in resource.data" :key="index">
                        <TableCell
                            v-for="column in getFilteredColumns"
                            :key="column.name"
                            :style="{ width: column.width, left: column.sticked ? column.left + 'px' : '' }"
                            :class="{
                                'sticky z-10 bg-white/90 hover:bg-muted/50 dark:bg-background/80': column.sticked,
                            }"
                        >
                            <div class="flex w-full items-center" :class="column.options.alignment" v-if="row[column.name] !== null">
                                <component
                                    :is="Columns[column.type as string as ColumnTypes]"
                                    :params="row._customColumnsParams"
                                    :name="column.name"
                                    :alignment="column.options.alignment"
                                    :class="cellClass(column)"
                                    :hasIcon="column.hasIcon"
                                    >{{ row[column.name] }}
                                </component>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <ScrollToRight />

            <Loader v-if="loading" class="absolute inset-0 z-10 flex justify-center bg-white/50" />
        </div>

        <EmptyState v-else />

        <Pagination
            :disabled="loading"
            :class="{ 'px-4': expanded, disabled: loading }"
            :meta="resource.meta"
            :stickyPagination
            :hidePageNumbers
            @update="onPageChange"
        ></Pagination>
    </div>
</template>

<style scoped>
table.scrolled tr:hover td.sticky {
    background-color: hsl(var(--muted) / 0.9);
}

table tr:hover td.sticky {
    background-color: hsl(var(--muted) / 0.5);
}
</style>
