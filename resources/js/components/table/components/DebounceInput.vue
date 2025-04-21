<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { useDebounceFn } from '@vueuse/core';

defineProps<{
    modelValue: string;
}>();

const model = defineModel<string>()

// Emit debounced update event
const emit = defineEmits<{
    (e: 'update', value: string): void
}>()

const debouncedFn = useDebounceFn(() => {
    if (model.value !== undefined) {
        emit('update', model.value)
    }
}, 300);

const onInput = () => debouncedFn();
</script>

<template>
    <Input v-model="model" @input="onInput" />
</template>
