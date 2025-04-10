<script lang="ts" setup>
import { computed } from 'vue';
import type { Image as TypeImage, ImageRecord, Icon as TypeIcon, IconRecord, LinkRecord } from '../columns';
import Image from '../Image.vue';
import Icon from '../Icon.vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    name: string;
    params: Record<string, ImageRecord | IconRecord | LinkRecord>;
    class: string;
}

const props = defineProps<Props>();

const position = computed(() => {
    const imageProps = props.params?.[props.name]?.image as TypeImage | null;
    const iconProps = props.params?.[props.name]?.icon as TypeIcon | null;
    return imageProps?.position ?? iconProps?.position ?? null;
});

const isSameDomain = (urlString) => {
    try {
        const url = new URL(urlString, location.href); // handles relative URLs too
        return url.origin === location.origin;
    } catch (_) {
        return false; // invalid URL
    }
}

const component = computed(() => {
    if (props.params?.[props.name]?.link) {
        const targetBlank = props.params?.[props.name]?.link?.target && props.params?.[props.name]?.link?.target === '_blank';
        return !isSameDomain(props.params?.[props.name]?.link.href) || targetBlank ? 'a' : Link;
    }
    return 'div';
});
</script>

<template>
    <component :is="component" v-bind="params?.[name]?.link ?? []"
               class="flex items-center space-x-2"
               :class="{
                    'flex-row-reverse': position === 'end',
                }"
    >
        <Icon :icon="params?.[name]?.icon as TypeIcon" />
        <Image :image="params?.[name]?.image as TypeImage" />
        <span :class="props.class"><slot /></span>
    </component>
</template>
