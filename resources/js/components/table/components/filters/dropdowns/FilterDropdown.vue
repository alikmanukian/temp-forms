<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import type { Clause, Filter } from '@/components/table';
import { ref, computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem
} from '@/components/ui/dropdown-menu';
import ClauseSymbol from '@/components/table/components/filters/dropdowns/ClauseSymbol.vue';
import * as Filters from '@/components/table/components/filters/inputs';

interface Props {
    filter: Filter;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', name: string, value: string, clause: string|null): void;
}>();

const selectedClause = ref<Clause>(props.filter.selectedClause ?? props.filter.clauses[0]);
const selectedFilter = ref<Filter>(props.filter);

const onChangeClause = (value: Clause) => {
    selectedClause.value = value;
    emit('update', props.filter.name, props.filter.value as string, value.searchSymbol);
}

const onChangeValue = (value: string) => {
    selectedFilter.value = {
        ...props.filter,
        value
    }
    emit('update', props.filter.name, value, selectedClause.value.searchSymbol);
}

const filterComponent = computed(() => {
    if (selectedFilter.value.component === 'DropdownFilter') {
        return Filters['ListFilter'];
    }

    return Filters[selectedFilter.value.component as keyof typeof Filters]
});
</script>

<template>
    <DropdownMenu class="relative">
        <div class="flex h-7 items-center overflow-hidden rounded-md text-sm shadow border border-border">
            <DropdownMenuTrigger class="group/filter flex h-full cursor-pointer items-center bg-gray-50 hover:bg-gray-100 hover:text-orange-600">
                <span class="px-2 text-left font-medium whitespace-nowrap">{{ filter.title }}</span>
                <span class="flex items-center px-1 text-center text-lg leading-none">
                    <ClauseSymbol :clause="selectedClause" />
                </span>
                <span class="px-2 text-left text-muted-foreground group-hover/filter:text-orange-600 truncate max-w-48">{{ filter.value }}</span>
            </DropdownMenuTrigger>
            <button @click.prevent="onChangeValue('')" class="h-full bg-gray-50 px-2 py-1 hover:bg-gray-100 hover:text-orange-600">
                <Icon name="X" class="size-3.5" />
            </button>
        </div>

        <DropdownMenuContent class="min-w-[300px] bg-gray-50 p-2 space-y-3 mt-1" align="start">
            <div class="flex gap-2 items-center">
                <label>Operator:</label>
                <DropdownMenu class="relative">
                    <DropdownMenuTrigger>
                        <div class="flex gap-1 items-center text-muted-foreground">
                            <ClauseSymbol :clause="selectedClause" />
                            <span>{{ selectedClause.name }}</span>
                            <Icon name="ChevronDown" class="w-5" />
                        </div>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent align="start">
                        <DropdownMenuItem v-for="clause in filter.clauses" :key="clause.value" @click="onChangeClause(clause)">
                            <div class="flex gap-1 items-center">
                                <ClauseSymbol :clause />
                                <span>{{ clause.name }}</span>
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <component
                v-model="selectedFilter.value"
                @update="onChangeValue"
                :is="filterComponent"
                :filter="selectedFilter"
            ></component>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
