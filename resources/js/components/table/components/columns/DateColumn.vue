<script setup lang="ts">
import type { LinkRecord } from '@/components/table/components/columns';
import Wrapper from '@/components/table/components/Wrapper.vue';

interface Props {
    name: string;
    params: Record<string, LinkRecord>;
    alignment: 'justify-start'|'justify-center'|'justify-end';
    class: string;
}

const props = defineProps<Props>();
</script>

<template>
    <Wrapper :name
             :params="params as Record<string, LinkRecord>"
             class="flex items-center gap-2"
    >
        <span :class="props.class"><slot /></span>
    </Wrapper>
</template>
