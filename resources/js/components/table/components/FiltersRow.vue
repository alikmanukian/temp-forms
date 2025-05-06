<script setup lang="ts">
import type { Clause, Filter } from '../index';
import { computed } from 'vue';
import { FilterDropdown } from './filters/dropdowns';
interface Props {
    filters: Record<string, Filter>;
}
const props = defineProps<Props>()

const appliedFilters = computed(() => Object.fromEntries(
    Object.entries(props.filters).filter(([_, config]) => config && config.selected)
));

const emit = defineEmits<{
    (e: 'update', name: string, value: string, clause: Clause|null): void;
    (e: 'delete', name: string): void;
}>();

const onUpdate = (name: string, value: string, clause: Clause|null) => {
    emit('update', name, value, clause)
}

const onDelete = (name: string) => {
    emit('delete', name)
}
</script>

<template>
    <div class="p-4 gap-4 flex items-center flex-wrap">
        <FilterDropdown v-for="filter in appliedFilters"
                        :key="filter.name"
                        :filter="filter"
                        @update="onUpdate"
                        @delete="onDelete"
        />
    </div>
</template>
