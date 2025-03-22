<script lang="ts" setup>
import type { TableHeader } from '@/components/table';

interface Props {
    column: TableHeader
}
defineProps<Props>()
</script>
<template>
    <div class="flex items-center h-full" :class="column.options.headerAlignment">
        <div class="truncate px-4 h-full flex items-center" :class="column.options.headerClass">
            {{ column.header }}
        </div>
    </div>
</template>
