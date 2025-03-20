<script setup lang="ts">
import RowsPerPage from '@/components/table/components/RowsPerPage.vue';
import ColumnsVisibility from '@/components/table/components/ColumnsVisibility.vue';
import { computed } from 'vue';
import { PaginatedMeta, TableHeader } from '@/types';

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

const fixedColumns = props.headers.filter((column: TableHeader) =>
        !column.options.toggleable).map((column: TableHeader) => column.name)
</script>

<template>
    <div class="flex justify-between items-center px-4 py-3">
        <div class="flex-1 text-sm font-medium">
            Showing <span class="font-bold">{{ from }}-{{ to }}</span> of <span class="font-bold">{{ meta.total }}</span> records
        </div>

        <div class="ml-auto flex items-center gap-4">
            <RowsPerPage :meta="meta"
                         :reloadOnly
                         includeQueryString
                         :pageName="pageName"
            />

            <ColumnsVisibility :columns="headers" :fixedColumns />
        </div>
    </div>
</template>
