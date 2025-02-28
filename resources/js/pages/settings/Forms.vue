<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { TextField, Form, Button, useForm } from '@/components/form';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
}, {
    url: route('forms.update'),
    method: 'patch'
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Forms" description="Testing custom forms" />

                <Form :form class="space-y-6">
                    <TextField label="Name"
                               name="name"
                               precognitive
                    />
                    <TextField label="Email address"
                               name="email"
                               type="email"
                               precognitive
                    />

                    <div class="flex items-center gap-4">
                        <Button>Save</Button>

                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-neutral-600">Saved.</p>
                        </TransitionRoot>
                    </div>
                </Form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
