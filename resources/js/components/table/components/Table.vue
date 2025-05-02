<script lang="ts" setup>
import type { Filter, Paginated, TableHeader as TableHeaderType } from '../index';
import Pagination from '../components/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed, nextTick, onMounted, onUnmounted, provide, reactive, ref, useTemplateRef, watch } from 'vue';
import vResizable from '../utils/resizable';
import EmptyState from '../components/EmptyState.vue';
import ToolsRow from '../components/ToolsRow.vue';
import { cn } from '@/lib/utils';
import HeaderButton from '../components/HeaderButton.vue';
import { init, useComponents } from '../utils/components';
import ScrollTableButton from '../components/ScrollTableButton.vue';
import { useScrollable } from '../utils/scrollable';
import * as Columns from './columns';
import * as Filters from './filters/inputs';
import DebounceInput from '../components/DebounceInput.vue';
import FiltersButton from '@/components/table/components/FiltersButton.vue';
import FiltersRow from '@/components/table/components/FiltersRow.vue';
import { useFilters } from '@/components/table/utils/filterable';
import { buildData } from '@/components/table/utils/helpers';
import Loader from './Loader.vue';
import { useRequest } from '@/components/table/utils/request';

interface Props {
    resource: Paginated<any>;
    expanded?: boolean;
    hidePageNumbers?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    expanded: false,
    hidePageNumbers: false,
});

const noResults = computed(() => props.resource.data.length === 0);
const name = props.resource.name;
const pageName = props.resource.pageName;

const { getFilteredColumns } = useComponents(name);
const { scrollPosition, scrollable, updateScrollPosition, updateScrollSize, updateContainerWidth, saveColumnsPositions } = useScrollable(name);
const { setSearchParams } = useFilters(pageName);

const container = useTemplateRef<HTMLElement>('container');

const stickyHeader = computed(() => props.resource.stickyHeader);
const stickyPagination = computed(() => props.resource.stickyPagination);

    const showFiltersRowInHeader = computed(() => {
    return props.resource.filters.some((filter: Filter) => {
        return filter.showInHeader;
    });
});

const columnWrappingMethod = (column: TableHeaderType) => {
    if (column.options.truncate == 1) {
        return 'truncate';
    }

    if (column.options.truncate > 1) {
        // line-clamp-2
        // line-clamp-3
        // line-clamp-4
        // line-clamp-5
        return 'line-clamp-' + column.options.truncate;
    }

    if (column.options.wrap) {
        return '';
    }

    if (!stickyHeader.value) {
        return 'whitespace-nowrap';
    }
};

const cellClass = (column: TableHeaderType) => {
    return cn([columnWrappingMethod(column), column.options.alignment, column.options.cellClass]);
};

const resizeObserver = new ResizeObserver(() => {
    updateContainerWidth();
    updateScrollSize();
    saveColumnsPositions();
});

onMounted(() => {
    init(props.resource);

    nextTick(() => {
        updateContainerWidth();
        updateScrollSize();
        saveColumnsPositions();
    });

    if (container.value) {
        resizeObserver.observe(container.value);
    }
});

onUnmounted(() => {
    if (container.value) {
        resizeObserver.unobserve(container.value);
        resizeObserver.disconnect();
    }
});

provide('name', name);
provide('pageName', pageName);

type ColumnTypes = keyof typeof Columns;

const onSearch = (field: string, value: string|string[], clause: string|null) => {
    if (Array.isArray(value)) {
        value = value.join(',')
    }

    if (clause) {
        value = clause + value;
    }

    reload(setSearchParams(field, value), {
        onSuccess: (response) => {
            const filter = (response.props[name] as Paginated<any>).filters.find((filter: Filter) => {
                return filter.name === field;
            });

            if (filter) {
                filters[field] = filter;
            }
        },
    });
};

const onPageChange = (page: number) => {
    reload(buildData(pageName, page));
};

const { reload, loading } = useRequest(props.resource.name);

const { getInitialSearch } = useFilters(pageName);

const search = ref<string>(getInitialSearch('search'))

// Initialize filters
const filters = reactive<Record<string, Filter>>({})
props.resource.filters.forEach((filter: Filter) => {
    filters[filter.name] = {...filter}
});
</script>

<template>
    <div class="@container" :class="{ '-mx-4': expanded }" ref="container" :data-name="`table-container-${name}`">
        <div class="flex space-x-3 p-4">
            <DebounceInput v-if="resource.searchable" v-model="search" @update="(value) => onSearch('search', value, '')" class="flex-1" />
            <FiltersButton />
        </div>

        <FiltersRow :filters="filters" @update="onSearch" />

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
                            }"
                        >
                            <component
                                v-if="filters[column.name]"
                                v-model="filters[column.name].value"
                                @update="(value: string|string[], clause: string|null) => onSearch(column.name, value, clause)"
                                :is="Filters[filters[column.name].component as keyof typeof Filters]"
                                :filter="resource.filters.find((filter: Filter) => filter.name === column.name)"
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
                                'sticky bg-white/90 hover:bg-muted/50 dark:bg-background/80': column.sticked,
                            }"
                        >
                            <div class="flex items-center w-full" :class="column.options.alignment">
                                <component
                                    :is="Columns[column.type as string as ColumnTypes]"
                                    :params="row._customColumnsParams"
                                    :name="column.name"
                                    :alignment="column.options.alignment"
                                    :class="cellClass(column)"
                                    >{{ row[column.name] }}
                                </component>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <ScrollTableButton />

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
