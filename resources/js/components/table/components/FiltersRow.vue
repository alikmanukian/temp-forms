<script setup lang="ts">
import type { Clause, Filter } from '../index';
import { computed, inject } from 'vue';
import { FilterDropdown } from './filters/dropdowns';

const filters = inject('filters') as Record<string, Filter>;

const appliedFilters = computed(() => Object.fromEntries(
    Object.entries(filters).filter(([_, config]) => config && config.selected)
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

    <div class="p-4 space-y-2" v-if="Object.keys(appliedFilters).length > 0">
        <div>Applied filters</div>
        <div class="gap-4 flex items-center flex-wrap border bg-muted/50 mt-1 p-2 rounded-lg">
            <FilterDropdown v-for="filter in appliedFilters"
                            :key="filter.name"
                            :filter="filter"
                            @update="onUpdate"
                            @delete="onDelete"
            />
        </div>
    </div>
</template>
