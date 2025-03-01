<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, FormProps } from '@/types';
import { Form, useForm } from '@/components/form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

interface Props {
    form: FormProps
}

const props = defineProps<Props>();

const form = useForm<FormProps>(props.form);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Forms" description="Testing custom forms" />

                <Form :form class="space-y-6" />
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
