<script setup lang="ts">
/**
 * Image component for displaying images.
 */
import { cn } from '@/lib/utils';
import { ref } from 'vue';

defineProps<{
    src: string;
    placeholder?: string;
    lazy?: boolean;
    priority?: 'high' | 'low' | 'auto';
    imageClass?: string | string[];
}>();

const loaded = ref<boolean>(false);

const onImageLoad = () => {
    loaded.value = true;
};

const defaultPlaceholderClass = 'absolute inset-0 h-full w-full object-cover transition-opacity duration-700 ease-in-out'
const defaultImageClass = 'relative h-full w-full object-cover transition-opacity duration-700 ease-in-out'
</script>

<template>
    <div class="relative overflow-hidden">
        <img v-if="placeholder"
             :src="placeholder"
             :class="cn(defaultPlaceholderClass, imageClass)"
             :loading="lazy ? 'lazy' : 'eager'"
             :fetchPriority="priority || 'auto'"
        />
        <img
            :src
            :class="cn(defaultImageClass, imageClass)"
            @load="onImageLoad"
            :style="{ opacity: !placeholder || loaded ? 1 : 0 }"
            :loading="lazy ? 'lazy' : 'eager'"
            :fetchPriority="priority || 'auto'"
        />
    </div>
</template>
