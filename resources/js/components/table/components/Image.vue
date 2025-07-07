<script lang="ts" setup>
import type { Image as ImageType } from './columns';
import Image from '@/components/core/components/Image.vue';
import { computed } from 'vue';

const props = defineProps<{
    image?: ImageType|null
}>()

const src = computed(() => {
    if (!props.image) {
        return null;
    }

    return typeof props.image?.url === 'string'
        ? props.image?.url
        : (props.image?.url?.[0] ?? null);
});

const placeholder = computed(() => {
    if (!src.value || src.value.startsWith('data:') || src.value.startsWith('http://') || src.value.startsWith('https://')) {
        return null;
    }

    const lastDot = src.value.lastIndexOf('.');
    const filename = src.value.substring(0, lastDot);
    const extension = src.value.substring(lastDot);

    return `${filename}-placeholder${extension}`;
});
</script>

<template>
    <Image v-if="image && src" v-bind="image" :src :placeholder />
</template>
