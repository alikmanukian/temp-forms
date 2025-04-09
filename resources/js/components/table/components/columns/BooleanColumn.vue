<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';
import type { Icon as TypeIcon } from '../columns';

interface Props {
    name: string
    params: Record<'BooleanColumn', Record<string, TypeIcon>>
    class: string
}

const props = defineProps<Props>()

const icon = computed(() => {
    const iconProps = props.params.BooleanColumn?.[props.name] as TypeIcon|null;
    return  iconProps?.icon ?? null;
});
</script>

<template>
    <div class="flex space-x-1 items-center justify-center">
        <Icon v-if="icon && icon.name && icon.position === 'start'"
              v-bind="icon" />
        <span :class="props.class"><slot /></span>
        <Icon v-if="icon && icon.name && icon.position === 'end'"
              v-bind="icon" />
    </div>
</template>
