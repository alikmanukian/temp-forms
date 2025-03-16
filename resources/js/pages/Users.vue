<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User, Paginated } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Table } from '@/components/table';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tables',
        href: '/tables',
    },
];

defineProps<{
    users: Paginated<User>;
}>();
</script>

<template>
    <Head title="Tables" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Table :resource="users" :reloadOnly="['users']" stickyPagination></Table>
        </div>
    </AppLayout>
</template>
