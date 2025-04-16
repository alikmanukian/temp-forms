<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { watch, inject } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';

const props = defineProps<{
    token: string;
}>();

// Get initial search from query string
const page = usePage<{
    query: {
        filter? : {
            [props.token]?: string;
        }
    };
}>();

const query = page.props.query;

const initialSearch = Object.hasOwn(query, 'filter')
    ? (query.filter?.[props.token] || '')
    : '';

const pageName = inject<string>('pageName') as string;

const form = useForm<{
    filter: {[props.token]: string};
}>({
    filter: {[props.token]: initialSearch},
    [pageName]: 1 // reset page to 1
});

const emit = defineEmits<{
    (e: 'start'): void,
    (e: 'end'): void,
}>();

const debouncedFn = useDebounceFn(() => {
    emit('start');

    // todo: maybe better to make search in table component. Here just fire event with search value
    form.get('', {
        preserveState: true,
        replace: true,
        onFinish: () => emit('end'),
    });
}, 300);

watch(
    () => form.filter[props.token],
    () => debouncedFn(),
    {
        deep: true,
    },
);
</script>

<template>
    <form @submit.prevent><Input v-model="form.filter[token]" /></form>
</template>
