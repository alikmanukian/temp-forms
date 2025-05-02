<script lang="ts" setup>
import Icon from '../Icon.vue';
import type { Icon as TypeIcon, IconRecord } from './index';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    name: string
    params: Record<string, IconRecord>
    class: string
}

const props = defineProps<Props>()

const position = computed(() => {
    const iconProps = props.params?.[props.name]?.icon as TypeIcon|null;
    return  iconProps?.position ?? null;
});

</script>

<template>
    <div class="flex gap-1 items-center justify-center" :class="cn([
        {
            'flex-row-reverse': position === 'end'
        },
        props.class,
    ])">
        <Icon :icon="params?.[name]?.icon as TypeIcon" />
        <span><slot /></span>
    </div>
</template>
