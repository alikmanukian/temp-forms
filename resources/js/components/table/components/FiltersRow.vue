<script setup lang="ts">
import type { Filter } from '../index';
import { computed } from 'vue';
interface Props {
    filters: Record<string, Filter>;
}
const props = defineProps<Props>()

const appliedFilters = computed(() => Object.fromEntries(
    Object.entries(props.filters).filter(([_, config]) => config && config.value !== null && config.value !== '')
));

const emit = defineEmits<{
    (e: 'update', name: string, value: string): void;
}>();

const clear = (name: string, value: string) => {
    emit('update', name, value)
}

</script>

<template>
    <div class="p-4 flex items-center gap-2">
        <div v-for="filter in appliedFilters" :key="filter.name">
            <div class="grid grid-cols-[auto_2rem_auto_2rem] gap-1 items-center bg-gray-100 p-2 rounded-md">
                <span class="text-left">{{ filter.name}}</span>
                <span class="text-center">{{ filter.selectedClause}}</span>
                <span class="text-left">{{ filter.value}}</span>
                <button @click.prevent="clear(filter.name, '')">&times;</button>
            </div>
        </div>
    </div>
</template>
