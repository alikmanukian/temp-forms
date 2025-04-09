<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import { type IconOrImage } from '../columns';

interface Props {
    name: string
    params: Record<string, Record<string, Record<'image'|'icon', IconOrImage>>>
    class: string
}

const props = defineProps<Props>()

const image = computed(() => {
    return  props.params.TextColumn?.[props.name]?.image ?? null;
});

const icon = computed(() => {
    return  props.params.TextColumn?.[props.name]?.icon ?? null;
});

</script>

<template>
    <div class="flex items-center space-x-2">
        <Icon v-if="icon && icon.name && icon.position === 'start'"
              :name="icon.name as string"
              v-bind="icon" />
        <img v-if="image && image.src && image.position === 'start'"
             v-bind="image"/>
        <span :class="props.class"><slot /></span>
        <Icon v-if="icon && icon.name && icon.position === 'end'"
              :name="icon.name as string"
              v-bind="icon" />
        <img v-if="image && image.src && image.position === 'end'"
             v-bind="image"/>
    </div>
</template>
