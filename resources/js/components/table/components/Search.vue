<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { watch, inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { useFilters } from '@/components/table/utils/filterable';

const props = defineProps<{
    token: string;
}>();

const pageName = inject<string>('pageName') as string;

const { getInitialSearch } = useFilters(pageName);

const form = useForm<{ search: string }>({
    search: getInitialSearch(props.token),
});

const emit = defineEmits<{
    (e: 'update', name: string, value: string): void;
}>();

const debouncedFn = useDebounceFn(() => {
    emit('update', props.token, form.search);
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
