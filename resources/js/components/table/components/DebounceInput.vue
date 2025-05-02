<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { useDebounceFn } from '@vueuse/core';
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    modelValue: string;
    icon?: string;
    class?: string|string[]|object;
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

const inputClass = computed(() => cn([props.class, {
    'pl-8': props.icon,
}]))
</script>

<template>
    <div class="relative">
        <span v-if="icon" class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
            <Icon :name="icon" class="size-4 text-muted-foreground" />
        </span>

        <Input v-model="model" @input="onInput" :class="inputClass"/>
    </div>

</template>
