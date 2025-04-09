<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import { type IconOrImage } from '../columns';

interface Props {
    name: string
    params: Record<string, Record<string, Record<'icon', IconOrImage>>>
}

const props = defineProps<Props>()

const icon = computed(() => {
    return  props.params.BooleanColumn?.[props.name]?.icon ?? '';
});
</script>

<template>
    <div class="flex space-x-1 items-center justify-center">
        <Icon v-if="icon && icon.name && icon.position === 'start'"
              :name="icon.name as string"
              v-bind="icon" />
        <span><slot /></span>
        <Icon v-if="icon && icon.name && icon.position === 'end'"
              :name="icon.name as string"
              v-bind="icon" />
    </div>
</template>
