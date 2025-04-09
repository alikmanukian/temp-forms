<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { computed } from 'vue';
import Icon from '@/components/Icon.vue';
import type { Image as TypeImage, Icon as TypeIcon } from '../columns';

interface TypeVariant {
    ['variant']: string
}

interface Props {
    name: string
    params: Record<'BadgeColumn', Record<string, TypeImage|TypeIcon|TypeVariant>>
    class: string
}

const props = defineProps<Props>()

const defaultClasses = 'space-x-1 inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset';

const badgeVariants = {
    default: 'bg-gray-50 text-gray-600 ring-gray-500/10',
    red: 'bg-red-50 text-red-700 ring-red-600/10',
    yellow: 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
    green: 'bg-green-50 text-green-700 ring-green-600/20',
    blue: 'bg-blue-50 text-blue-700 ring-blue-700/10',
    indigo: 'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    purple: 'bg-purple-50 text-purple-700 ring-purple-700/10',
    pink: 'bg-pink-50 text-pink-700 ring-pink-700/10'
} as const;

type Variant = keyof typeof badgeVariants;

const variant = computed(() => {
    const variantProps = props.params.BadgeColumn?.[props.name] as TypeVariant|null;
    const key = (variantProps?.variant ?? 'default') as string;
    return  badgeVariants[key as Variant] ?? badgeVariants.default;
});

const icon = computed(() => {
    const iconProps = props.params.BadgeColumn?.[props.name] as TypeIcon|null;
    return  iconProps?.icon ?? null;
});

</script>

<template>
    <span :class="cn(defaultClasses, variant)">
        <Icon v-if="icon && icon.name && icon.position === 'start'"
              v-bind="icon" />
        <span :class="props.class"><slot /></span>
        <Icon v-if="icon && icon.name && icon.position === 'end'"
              v-bind="icon" />
    </span>
</template>
