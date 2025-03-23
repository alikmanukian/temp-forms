<script lang="ts" setup>
import type { Paginated, TableHeader as TableHeaderType } from '../index';
import Pagination from '@/components/table/components/Pagination.vue';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed, onMounted, ref, useTemplateRef } from 'vue';
import { useLocalStorage } from '@vueuse/core';
import vResizable from '../utils/resizable';
import EmptyState from '@/components/table/components/EmptyState.vue';
import ToolsRow from '@/components/table/components/ToolsRow.vue';
import { cn } from '@/lib/utils';
import HeaderButton from '@/components/table/components/HeaderButton.vue';
import { useToggleColumns } from '@/components/table/utils/toggleColumns';

interface Props {
    resource: Paginated<any>;
    reloadOnly?: boolean | string[];
    includeQueryString?: boolean;
    resizable?: boolean;
    fixed?: boolean;
    expanded?: boolean;
    hidePageNumbers?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    includeQueryString: true,
    resizable: false,
    fixed: false,
    expanded: false,
    hidePageNumbers: false,
});

const noResults = computed(() => props.resource.data.length === 0);

const { columns, filteredColumns } = useToggleColumns(props.resource.headers)

const stickyHeader = computed(() => props.fixed && props.resource.stickyHeader);
const stickyPagination = computed(() => props.resource.stickyPagination);

const scrollPosition = ref(0);
const columnsPositions = ref<Record<string, {left: number, width: number}>>({});
const container = useTemplateRef('container');


const columnWrappingMethod = (column: TableHeaderType) => {
    if (!props.fixed) {
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

const lastStickableColumn = computed(() => {
    return columns.value.filter((column) => column.options.stickable).pop();
});

const resizable = computed(() => props.resizable && props.fixed);

const onTableResizeEnd = (e: CustomEvent) => {
    const el = e.target as HTMLElement;
    el.querySelectorAll('th').forEach((th: HTMLElement) => {
        const col = columns.value.find((column: TableHeaderType) => column.name == th.dataset.name);
        if (col) {
            col.width = th.style.width;
        }
    });

    saveColumnsPositions();
};

const onTableScroll = (e: Event) => {
    const target = e.target as HTMLElement;

    if (target.scrollLeft === 0) {
        scrollPosition.value = 0;
    }

    if (target?.scrollLeft > 0 && scrollPosition.value === 0) {
        scrollPosition.value = target.scrollLeft;
    }
}

const saveColumnsPositions = () => {
    const table = document.querySelector("table");
    if (!table) return;

    const tableLeft = table.getBoundingClientRect().left;

    container.value?.querySelectorAll('thead th')
        .forEach((th) => {
            const thElement = th as HTMLElement; // ✅ Type assertion
            const name = thElement.dataset.name as string
            columnsPositions.value[name] = {
                left: th.getBoundingClientRect().left - tableLeft,
                width: th.getBoundingClientRect().width,
            };
        });
}

onMounted(() => {
    saveColumnsPositions();
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
                @resize:end="onTableResizeEnd"
                :style="{ overflow: stickyHeader ? 'visible' : 'auto' }"
                @scroll="onTableScroll"
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
                            :style="{ width: column.width, left: !fixed ? columnsPositions[column.name]?.left + 'px' : 'auto'}"
                            :key="column.name"
                            :data-name="column.name"
                            class="px-0"
                            :class="{
                                'sticky z-10 bg-gray-50/90 dark:bg-background/80 p-4': !fixed && column.options.stickable,
                                'sticky-last': !fixed && column.options.stickable && column.name == lastStickableColumn?.name
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
                                   :style="{ width: column.width, left: columnsPositions[column.name]?.left + 'px' }"
                                   :class="{
                                       'sticky z-10 bg-white/90 dark:bg-background/80': !fixed && column.options.stickable,
                                       'sticky-last': !fixed && column.options.stickable && column.name == lastStickableColumn?.name
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
