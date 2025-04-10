<script setup lang="ts">
import type { LinkRecord } from './columns';
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    name: string;
    params: Record<string, LinkRecord>;
}

const props = defineProps<Props>();

const isSameDomain = (url: string) => {
    try {
        return (new URL(url, location.href)).origin === location.origin;
    } catch (_) {
        return false; // invalid URL
    }
}

const component = computed(() => {
    const linkRecord = props.params?.[props.name] as LinkRecord
    if (linkRecord?.link) {
        const targetBlank = linkRecord?.link?.target && linkRecord?.link?.target === '_blank';
        return !isSameDomain(linkRecord?.link.href) || targetBlank ? 'a' : Link;
    }
    return 'div';
});
</script>

<template>
    <component :is="component" v-bind="(params?.[name] as LinkRecord)?.link ?? []">
        <slot />
    </component>
</template>
