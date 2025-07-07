<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import type { Paginated } from '@/components/table'
import { Head } from '@inertiajs/vue3';
import { Table } from '@/components/table';
import Image from '@/components/core/components/Image.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tables',
        href: '/tables',
    },
];

defineProps<{
    users: Paginated<User>;
    // employees: Paginated<User>;
}>();
</script>

<template>
    <Head title="Tables" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 pb-0">
            <div class="grid grid-cols-5 gap-4">
                <Image src="/images/001.jpg" placeholder="/images/001-placeholder.jpg" />
                <Image src="/images/002.jpg" placeholder="/images/002-placeholder.jpg" />
                <Image src="/images/003.jpg" placeholder="/images/003-placeholder.jpg" />
                <Image src="/images/004.jpg" placeholder="/images/004-placeholder.jpg" />
                <Image src="/images/005.jpg" placeholder="/images/005-placeholder.jpg" />
            </div>

            <Table :resource="users"
                   hidePageNumbers
                   expanded
            />

<!--            <Table :resource="employees"-->
<!--                   :reloadOnly="['employees']"-->
<!--                   hidePageNumbers-->
<!--            />-->
        </div>
    </AppLayout>
</template>
