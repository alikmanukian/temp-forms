<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { watch, inject } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { buildData } from '../utils/helpers';

const props = defineProps<{
    token: string;
}>();

// Get initial search from query string
const page = usePage<{
    query: any;
}>();

const query = page.props.query;

const pageName = inject<string>('pageName') as string;

let filterParam = 'filter';

if (pageName.includes('.')) {
    filterParam += '.' + pageName.split('.')[1];
}
filterParam += '.' + props.token;

const initialSearch = filterParam.split('.').reduce((acc, key) => acc?.[key], query)

const form = useForm<{search: string}>({
    search: initialSearch,
});

const emit = defineEmits<{
    (e: 'start'): void;
    (e: 'end'): void;
}>();

const debouncedFn = useDebounceFn(() => {
    emit('start');

    // todo: maybe better to make search in table component. Here just fire event with search value
    form
        .transform((data) => ({
            ...buildData(filterParam, data.search),
            ...buildData(pageName, 1)
        }))
        .get('', {
            preserveState: true,
            replace: true,
            onFinish: () => emit('end'),
        });
}, 300);

watch(
    () => form.search,
    () => debouncedFn(),
    {
        deep: true,
    },
);
</script>

<template>
    <Input v-model="form.search" />
</template>
