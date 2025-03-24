<script lang="ts" setup>
import type { Paginated, TableHeader as TableHeaderType } from '../index';
import Pagination from '@/components/table/components/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed, onMounted, useTemplateRef } from 'vue';
import vResizable from '../utils/resizable';
import EmptyState from '@/components/table/components/EmptyState.vue';
import ToolsRow from '@/components/table/components/ToolsRow.vue';
import { cn } from '@/lib/utils';
import HeaderButton from '@/components/table/components/HeaderButton.vue';
import { useToggleColumns } from '@/components/table/utils/toggleColumns';
import { useStickableColumns } from '@/components/table/utils/stickColumns';

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
const fixed = computed(() => props.resource.fixed);

const { filteredColumns } = useToggleColumns(props.resource.headers)
const { lastStickableColumn, scrollPosition, updateScrollPosition, saveColumnsPositions } = useStickableColumns(props.resource.headers, 'container')

const stickyHeader = computed(() => fixed.value && props.resource.stickyHeader);
const stickyPagination = computed(() => props.resource.stickyPagination);

const columnWrappingMethod = (column: TableHeaderType) => {
    if (!fixed.value) {
        return 'whitespace-nowrap';
    }

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
    return cn([columnWrappingMethod(column), column.options.cellClass]);
};

const resizable = computed(() => props.resizable && fixed.value);

const container = useTemplateRef<HTMLElement>('container')

onMounted(() => {
    saveColumnsPositions(container.value);
});

</script>

<template>
    <div class="@container" :class="{ '-mx-4': expanded }" ref="container">
        <ToolsRow
            v-if="!noResults"
            :meta="resource.meta"
            :pageName="resource.pageName"
            :headers="resource.headers"
            :reloadOnly
            :class="{ 'px-4': expanded }"
        />

        <div v-if="!noResults" :class="{ 'border-b border-t': expanded, 'rounded-lg border': !expanded }">
            <Table
                v-resizable="resizable"
                @resize:end="(e) => saveColumnsPositions(container)"
                :style="{ overflow: stickyHeader ? 'visible' : 'auto' }"
                @scroll="updateScrollPosition"
                :class="{ 'table-fixed': fixed, 'scrolled': scrollPosition > 0 }"
            >
                <colgroup v-if="!resizable">
                    <col :style="{ width: column.width }" v-for="column in filteredColumns" :key="column.name" />
                </colgroup>

                <TableHeader
                    class="dark:text-white bg-gray-50 dark:bg-black/25 text-left text-xs font-medium uppercase tracking-wide"
                    :class="{ 'sticky top-0 z-10 bg-opacity-75 shadow-md shadow-gray-300/25 backdrop-blur backdrop-filter': stickyHeader }"
                >
                    <TableRow>
                        <TableHead
                            v-for="column in filteredColumns"
                            :style="{ width: column.width, left: !fixed ? column.left + 'px' : 'auto'}"
                            :key="column.name"
                            :data-name="column.name"
                            class="px-0"
                            :class="{
                                'sticky z-10 bg-gray-50/90 dark:bg-background/80': !fixed && column.options.sticked,
                                'sticky-last': !fixed && column.options.sticked && column.name == lastStickableColumn?.name
                            }"
                        >
                            <HeaderButton :column />
                        </TableHead>
                    </TableRow>
                </TableHeader>

                <TableBody>
                    <TableRow v-for="(row, index) in resource.data" :key="index">
                        <TableCell v-for="column in filteredColumns"
                                   :key="column.name"
                                   :style="{ width: column.width, left: column.left + 'px' }"
                                   :class="{
                                       'sticky z-10 bg-white/90 dark:bg-background/80': !fixed && column.options.sticked,
                                       'sticky-last': !fixed && column.options.sticked && column.name == lastStickableColumn?.name
                                   }"
                        >
                            <div class="flex items-center" :class="column.options.alignment">
                                <div :class="cellClass(column)">
                                    {{ row[column.name] }}
                                </div>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <EmptyState v-else />

        <Pagination
            :meta="resource.meta"
            :pageName="resource.pageName"
            :reloadOnly
            :stickyPagination
            :includeQueryString
            :hidePageNumbers
            :class="{ 'px-4': expanded }"
        ></Pagination>
    </div>
</template>

<style scoped>
table.scrolled tr td.sticky.sticky-last,
table.scrolled tr th.sticky.sticky-last {
    display: block;
    box-shadow: 1px 0 1px hsl(var(--border))
}
</style>
