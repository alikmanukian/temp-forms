<script setup lang="ts">
import ColumnsVisibility from '../components/ColumnsVisibility.vue';
import RowsPerPage from '../components/RowsPerPage.vue';
import type { PaginatedMeta, TableHeader } from '../index';
import { computed } from 'vue';

interface Props {
    meta: PaginatedMeta;
    pageName: string;
    headers: TableHeader[];
    reloadOnly?: boolean | string[];
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
});

const from = computed(() => (props.meta.currentPage - 1) * props.meta.perPage + 1);
const to = computed(() => props.meta.currentPage * props.meta.perPage);

const fixedColumns = props.headers.filter((column: TableHeader) => !column.options.toggleable).map((column: TableHeader) => column.name);
</script>

<template>
    <div class="@lg:flex-row @lg:space-y-0 flex flex-col items-center justify-end space-y-4 py-3">
        <div class="@lg:self-center flex-1 self-end text-sm font-medium">
            <span>Showing </span><span class="font-bold">{{ from }}-{{ to }}</span
            ><span> of </span><span class="font-bold">{{ meta.total }}</span
            ><span> records</span>
        </div>

        <div class="ml-auto flex flex-col gap-4 @sm:flex-row @sm:items-center items-end">
            <RowsPerPage :meta="meta" :reloadOnly includeQueryString v-if="meta.perPageOptions" />

            <ColumnsVisibility :fixedColumns />
        </div>
    </div>
</template>
