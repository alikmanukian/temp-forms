<script setup lang="ts">
import type { Filter, FilterOption } from '../index';
import { computed } from 'vue';
import { FilterDropdown } from './filters/dropdowns';
interface Props {
    filters: Record<string, Filter>;
}
const props = defineProps<Props>()

const appliedFilters = computed(() => Object.fromEntries(
    Object.entries(props.filters).filter(([_, config]) => config && config.value !== null && config.value !== '')
));

const emit = defineEmits<{
    (e: 'update', name: string, value: string, clause: string|null): void;
}>();

const onUpdate = (name: string, value: string, clause: string|null) => {
    emit('update', name, value, clause)
}

</script>

<template>
    <div class="p-4 gap-4 flex items-center flex-wrap">
        <FilterDropdown v-for="filter in appliedFilters"
                        :key="filter.name"
                        :filter="filter"
                        @update="onUpdate"
        />
    </div>
</template>
