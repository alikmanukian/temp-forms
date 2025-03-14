<script lang="ts" setup>
import { type Paginated } from '@/types';
import Pagination from '@/components/table/components/Pagination.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'

defineProps<{
    resource: Paginated<any>
}>()
</script>

<template>
    <div>
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

        <Pagination :meta="resource.meta"></Pagination>
    </div>
</template>
