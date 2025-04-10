<script lang="ts" setup>
import type { Image } from './columns';
import { computed } from 'vue';

const props = defineProps<{
    image?: Image|null
}>()

const src = computed(() => {
    if (!props.image) {
        return null;
    }

    return typeof props.image?.url === 'string'
        ? props.image?.url
        : (props.image?.url?.[0] ?? null);
});
</script>

<template>
    <img v-if="image && src" v-bind="image" :src/>
</template>
