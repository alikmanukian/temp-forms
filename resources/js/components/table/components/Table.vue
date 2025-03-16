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

interface Props {
    resource: Paginated<any>
    reloadOnly: boolean | string[]
    stickyPagination: boolean
    includeQueryString: boolean
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    stickyPagination: false,
    includeQueryString: true
});

const from = computed(() => (props.resource.meta.currentPage - 1) * props.resource.meta.perPage + 1)
const to = computed(() => props.resource.meta.currentPage * props.resource.meta.perPage)
</script>

<template>
    <div class="relative" :class="{'pb-20': stickyPagination}">
        <div class="flex justify-between">
            <div class="flex-1 text-sm font-medium">Showing <span class="font-bold">{{from}}-{{to}}</span> of <span class="font-bold">{{resource.meta.total}}</span> records</div>

            <RowsPerPage :meta="resource.meta"
                         :reloadOnly
                         :includeQueryString
                         :pageName="resource.pageName" />
        </div>


        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead v-for="(title, name) in resource.headers" :key="name">{{ title }}</TableHead>
                </TableRow>
            </TableHeader>

            <TableBody>
                <TableRow v-for="(row, index) in resource.data" :key="index">
                    <TableCell v-for="(_, name) in resource.headers" :key="name">
                        {{ row[name] }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>


        <Pagination :meta="resource.meta"
                    :pageName="resource.pageName"
                    :reloadOnly
                    :stickyPagination
                    :includeQueryString
        ></Pagination>
    </div>
</template>
