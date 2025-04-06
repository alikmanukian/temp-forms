<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import { inject } from 'vue';
import { useScrollable } from '../utils/scrollable';

const pageName = inject<string>('pageName') ?? 'page'

const { scrollToRight, showScrollButton, scrollable } = useScrollable(pageName);
</script>

<template>
    <button v-if="scrollable && showScrollButton"
            @click="scrollToRight"
            class="absolute top-0 right-0 w-8 h-12 z-10 bg-gray-200 hover:shadow-inner hover:bg-gray-300 flex items-center justify-center">
        <Icon name="ChevronsRight" class="w-4 h-4 text-gray-900" />
    </button>
</template>
