<script lang="ts" setup>
import { computed } from 'vue';
import type { Image as TypeImage, ImageRecord, Icon as TypeIcon, IconRecord, LinkRecord } from '../columns';
import Image from '../Image.vue';
import Icon from '../Icon.vue';

interface Props {
    name: string
    params: Record<string, ImageRecord|IconRecord|LinkRecord>
    class: string
}

const props = defineProps<Props>()

const position = computed(() => {
    const imageProps = props.params?.[props.name]?.image as TypeImage|null;
    const iconProps = props.params?.[props.name]?.icon as TypeIcon|null;
    return  imageProps?.position ?? iconProps?.position ?? null;
});

</script>

<template>
    <div class="flex items-center space-x-2" :class="{
        'flex-row-reverse': position === 'end'
    }">
        <Icon :icon="params?.[name]?.icon as TypeIcon" />
        <Image :image="params?.[name]?.image as TypeImage" />
        <span :class="props.class"><slot /></span>
    </div>
</template>
