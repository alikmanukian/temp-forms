<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { watch } from 'vue';
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
console.log(initialSearch);

const form = useForm<{
    filter: {[props.token]: string};
}>({
    filter: {[props.token]: initialSearch},
});

const emit = defineEmits<{
    (e: 'start'): void,
    (e: 'end'): void,
}>();

const debouncedFn = useDebounceFn(() => {
    emit('start');

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
    <div class="p-4">
        <form @submit.prevent><Input v-model="form.filter[token]" /></form>
    </div>
</template>
