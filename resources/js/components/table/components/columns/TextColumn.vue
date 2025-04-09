<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import type { Image as TypeImage, Icon as TypeIcon, Link as TypeLink } from '../columns';

interface Props {
    name: string
    params: Record<'TextColumn', Record<string, TypeImage|TypeIcon|TypeLink>>
    class: string
}

const props = defineProps<Props>()

const image = computed(() => {
    const imageProps = props.params.TextColumn?.[props.name] as TypeImage|null;
    return  imageProps?.image ?? null;
});

const icon = computed(() => {
    const iconProps = props.params.TextColumn?.[props.name] as TypeIcon|null;
    return  iconProps?.icon ?? null;
});

</script>

<template>
    <div class="flex items-center space-x-2">
        <Icon v-if="icon && icon.name && icon.position === 'start'"
              v-bind="icon" />
        <img v-if="image && image.src && image.position === 'start'"
             v-bind="image"/>
        <span :class="props.class"><slot /></span>
        <Icon v-if="icon && icon.name && icon.position === 'end'"
              v-bind="icon" />
        <img v-if="image && image.src && image.position === 'end'"
             v-bind="image"/>
    </div>
</template>
