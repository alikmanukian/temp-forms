<script lang="ts" setup>
import Icon from '../Icon.vue';
import type { Icon as TypeIcon, IconRecord } from './index';
import { computed } from 'vue';

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
    <div class="flex space-x-1 items-center justify-center" :class="{
        'flex-row-reverse': position === 'end'
    }">
        <Icon :icon="params?.[name]?.icon as TypeIcon" />
        <span :class="props.class"><slot /></span>
    </div>
</template>
