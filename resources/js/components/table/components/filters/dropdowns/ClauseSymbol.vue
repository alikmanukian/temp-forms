<script lang="ts" setup>
import type { Clause, Filter } from '@/components/table';
import Icon from '@/components/Icon.vue';

interface Props {
    clause: Clause;
}

defineProps<Props>();
</script>

<template>
    <div class="flex items-center justify-center w-5 h-5">
        <span v-if="clause.symbol[0] === '!'">!</span>
        <Icon :name="clause.symbol[0] === '!' ? clause.symbol.substring(1) : clause.symbol" class="size-4" />
    </div>
</template>
