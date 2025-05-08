<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
    DropdownMenuItem
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import type { Filter } from '@/components/table';
import { computed, inject, Ref } from 'vue';
import { useFilters } from '@/components/table/utils/filterable';

interface Props {
    resetSearchString: () => void;
}

const props = defineProps<Props>()

const emit = defineEmits<{
    (e: 'update', name: string): void;
}>()

const onAddFilter = (name: string) => {
    emit('update', name);
}

const pageName = inject('pageName') as string;
const name = inject('name') as string;
const filters = inject('filters') as Record<string, Filter>;
const filtersAreApplied = inject('filtersAreApplied') as boolean;

const { resetSearch } = useFilters(pageName, name, filters);

const unAppliedFilters = computed(() => Object.fromEntries(
    Object.entries(filters).filter(([_, config]) => !config || !config.selected)
));

const resetFilters = () => {
    resetSearch();
    props.resetSearchString();
}
</script>

<template>
    <DropdownMenu class="relative">
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="flex items-center relative h-10"
                :class="{'ring-1 ring-green-600 shadow-md text-green-700': filtersAreApplied}">
                <Icon name="Filter" class="mr-1 w-4 h-4"/>
                <span class="text-sm">Filters</span>
                <button v-if="filtersAreApplied" @click.stop.prevent="resetFilters()" class="absolute -top-1.5 -right-1.5 bg-green-700 text-green-50 rounded size-4 flex items-center justify-center">
                    <Icon name="X" class="pointer-events-none size-2"></Icon>
                </button>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Add filter</DropdownMenuLabel>
            <DropdownMenuSeparator class="bg-gray-300" />

            <template v-if="Object.keys(unAppliedFilters).length > 0">
                <DropdownMenuItem v-for="filter in unAppliedFilters"
                    :key="filter.name" @click="onAddFilter(filter.name)"
                >
                    <Icon name="Plus" class="!size-3" /><span>{{filter.title}}</span>
                </DropdownMenuItem>
            </template>
            <template v-else>
                <DropdownMenuItem class="flex items-center gap-2">
                    <span>No filters available</span>
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

<style scoped>
.pulse {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    50% {
        transform: scale(1.3);
        opacity: 0.8;
    }
}
</style>
