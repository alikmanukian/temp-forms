<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { inject } from 'vue';
import { useScrollable } from '../utils/scrollable';

const name = inject<string>('name') as string

const { scrollToRight, showScrollButton, scrollable } = useScrollable(name);
</script>

<template>
    <button v-if="scrollable && showScrollButton"
            @click="scrollToRight"
            class="absolute top-0 right-0 w-8 h-12 z-10 bg-gray-200 dark:bg-gray-700/95 dark:hover:bg-gray-800/95  hover:shadow-inner hover:bg-gray-300 flex items-center justify-center">
        <Icon name="ChevronsRight" class="w-4 h-4 text-gray-900 dark:text-gray-500" />
    </button>
</template>
