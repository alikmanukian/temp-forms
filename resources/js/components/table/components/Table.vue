<script lang="ts" setup>
import type { Paginated, TableHeader as TableHeaderType } from '../index';
import Pagination from '../components/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed, nextTick, onMounted, onUnmounted, provide, useTemplateRef } from 'vue';
import vResizable from '../utils/resizable';
import EmptyState from '../components/EmptyState.vue';
import ToolsRow from '../components/ToolsRow.vue';
import { cn } from '@/lib/utils';
import HeaderButton from '../components/HeaderButton.vue';
import { init, useComponents } from '../utils/components';
import ScrollTableButton from '../components/ScrollTableButton.vue';
import { useScrollable } from '../utils/scrollable';
import * as Columns from './columns';

interface Props {
    resource: Paginated<any>;
    reloadOnly?: boolean | string[];
    includeQueryString?: boolean;
    resizable?: boolean;
    expanded?: boolean;
    hidePageNumbers?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    includeQueryString: true,
    resizable: false,
    expanded: false,
    hidePageNumbers: false,
});

const noResults = computed(() => props.resource.data.length === 0);
const pageName = props.resource.pageName

const { getFilteredColumns } = useComponents(pageName)
const { scrollPosition, scrollable, updateScrollPosition, updateScrollSize, updateContainerWidth, saveColumnsPositions } = useScrollable(pageName)

const container = useTemplateRef<HTMLElement>('container')
const resizable = computed(() => props.resizable);

const stickyHeader = computed(() => props.resource.stickyHeader);
const stickyPagination = computed(() => props.resource.stickyPagination);

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
    return cn(['w-full', columnWrappingMethod(column), column.options.cellClass]);
};

const resizeObserver = new ResizeObserver(() => {
        updateContainerWidth();
        updateScrollSize();
        saveColumnsPositions();
});

onMounted(() => {
    init(props.resource)

    nextTick(() => {
        updateContainerWidth();
        updateScrollSize();
        saveColumnsPositions();
    })

    if (container.value) {
        resizeObserver.observe(container.value);
    }
});

onUnmounted(() => {
    if (container.value) {
        resizeObserver.unobserve(container.value);
        resizeObserver.disconnect();
    }
})

provide('pageName', pageName)

type ColumnTypes = keyof typeof Columns

</script>

<template>
    <div class="@container" :class="{ '-mx-4': expanded }" ref="container" :data-name="`table-container-${pageName}`">
        <ToolsRow
            v-if="!noResults"
            :meta="resource.meta"
            :pageName="resource.pageName"
            :headers="resource.headers"
            :reloadOnly
            :class="{ 'px-4': expanded }"
        />

        <div v-if="!noResults" class="relative" :class="{ 'border-b border-t': expanded, 'rounded-lg border': !expanded }">
            <Table
                v-resizable="resizable"
                @resize:end="saveColumnsPositions"
                :style="{ overflow: scrollable ? 'auto' : 'visible'}"
                @scroll="updateScrollPosition"
                :class="{ 'table-fixed': true, 'scrolled': scrollPosition > 0 }"
                data-name="scroll-container"
            >
                <colgroup v-if="!resizable">
                    <col :style="{ width: column.width }" v-for="column in getFilteredColumns" :key="column.name" />
                </colgroup>

                <TableHeader
                    class="dark:text-white bg-gray-50 dark:bg-black/25 text-left text-xs font-medium uppercase tracking-wide"
                    :class="{ 'sticky top-0 z-10 bg-opacity-75 shadow-md shadow-gray-300/25 backdrop-blur backdrop-filter': stickyHeader }"
                >
                    <TableRow>
                        <TableHead
                            v-for="column in getFilteredColumns"
                            :style="{ width: column.width, left: column.sticked ? column.left + 'px' : ''}"
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
                </TableHeader>

                <TableBody>
                    <TableRow v-for="(row, index) in resource.data" :key="index">
                        <TableCell v-for="column in getFilteredColumns"
                                   :key="column.name"
                                   :style="{ width: column.width, left: column.sticked ? column.left + 'px' : '' }"
                                   :class="{
                                       'sticky bg-white/90 dark:bg-background/80 hover:bg-muted/50': column.sticked,
                                   }"
                        >
                            <div class="flex items-center" :class="column.options.alignment">
                                <div :class="cellClass(column)">
                                    <component :is="Columns[column.type as string as ColumnTypes]"
                                               :params="row._params"
                                               :name="column.name"
                                               :class="cellClass(column)"
                                    >{{ row[column.name] }}</component>
                                </div>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <ScrollTableButton />
        </div>

        <EmptyState v-else />

        <Pagination
            :class="{ 'px-4': expanded }"
            :meta="resource.meta"
            :reloadOnly
            :stickyPagination
            :includeQueryString
            :hidePageNumbers
        ></Pagination>
    </div>
</template>

<style scoped>
table.scrolled tr:hover td.sticky {
    background-color: hsl(var(--muted) / 0.9)
}
table tr:hover td.sticky {
    background-color: hsl(var(--muted) / 0.5)
}
</style>
