<script lang="ts" setup>
import { computed } from 'vue';
import type { Image as TypeImage, ImageRecord, Icon as TypeIcon, IconRecord, LinkRecord } from './index';
import Image from '../Image.vue';
import Icon from '../Icon.vue';
import Wrapper from '../Wrapper.vue';

interface Props {
    name: string;
    params: Record<string, ImageRecord | IconRecord | LinkRecord>;
    class: string;
}

const props = defineProps<Props>();

const position = computed(() => {
    const imageProps = (props.params?.[props.name] as ImageRecord)?.image as TypeImage | null;
    const iconProps = (props.params?.[props.name] as IconRecord)?.icon as TypeIcon | null;
    return imageProps?.position ?? iconProps?.position ?? null;
});

</script>

<template>
    <Wrapper :name
             :params="params as Record<string, LinkRecord>"
             class="flex items-center space-x-2"
             :class="{'flex-row-reverse': position === 'end'}"
    >
        <Icon :icon="(params?.[name] as IconRecord)?.icon as TypeIcon" />
        <Image :image="(params?.[name] as ImageRecord)?.image as TypeImage" />
        <span :class="props.class"><slot /></span>
    </Wrapper>
</template>
