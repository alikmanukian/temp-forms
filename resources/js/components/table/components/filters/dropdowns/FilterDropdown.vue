<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import type { Clause, DropdownFilter, Filter } from '@/components/table';
import * as Filters from '@/components/table/components/filters/inputs';
import { clauseIsArrayable, clauseShouldNotHaveValue } from '@/components/table/utils/filterable';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { computed, inject, nextTick, ref, watch } from 'vue';

interface Props {
    filter: Filter | DropdownFilter;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', name: string, value: string, clause: Clause | null): void;
    (e: 'delete', name: string): void;
}>();

const name = inject('name') as string;

const selectedClause = ref<Clause>(props.filter.selectedClause ?? props.filter.clauses[0]);
const selectedFilter = ref<Filter | DropdownFilter>(props.filter);
const showComponent = computed<boolean>(() => !clauseShouldNotHaveValue(selectedClause.value));

const onChangeClause = (value: Clause) => {
    selectedClause.value = value;
    selectedFilter.value.selected = true;
    selectedFilter.value.selectedClause = value;

    if (clauseShouldNotHaveValue(selectedClause.value)) {
        selectedFilter.value.value = '';
    }

    if (!clauseIsArrayable(selectedClause.value) && Array.isArray(selectedFilter.value.value)) {
        selectedFilter.value.value = [];
        emit('update', props.filter.name, selectedFilter.value.value as string, selectedClause.value);
    }

    if (clauseShouldNotHaveValue(selectedClause.value) || selectedFilter.value.value) {
        emit('update', props.filter.name, selectedFilter.value.value as string, selectedClause.value);
    }

    // Focus input after clause change if there's an input to focus
    if (showComponent.value) {
        focusInputElement();
    }
};

const onChangeValue = (value: string) => {
    // selectedFilter.value.value = value;
    selectedFilter.value.opened = false;
    emit('update', props.filter.name, value, selectedClause.value);
};

const onDeleteFilter = () => {
    emit('delete', props.filter.name);
};

const filterComponent = computed(() => {
    if (selectedFilter.value.component === 'DropdownFilter') {
        return Filters['ListFilter'];
    }

    return Filters[selectedFilter.value.component as keyof typeof Filters];
});

watch(
    () => props.filter,
    (newFilter: Filter | DropdownFilter) => {
        selectedFilter.value = newFilter;
        selectedClause.value = newFilter.selectedClause ?? newFilter.clauses[0];
    },
);

const placeholder = ` `;

const label = computed(() => {
    if (selectedFilter.value.component === 'DropdownFilter' || selectedFilter.value.component === 'BooleanFilter') {
        if ((selectedFilter.value as DropdownFilter)?.multiple) {
            if (Array.isArray(selectedFilter.value.value)) {
                switch (selectedFilter.value.value.length) {
                    case 0:
                        return placeholder;
                    case 1:
                        return (selectedFilter.value as DropdownFilter)?.options.find((option) => selectedFilter.value.value[0] == option.value)
                            ?.label;
                    default:
                        return selectedFilter.value.value.length + ' items';
                }
            }
        }

        if (clauseShouldNotHaveValue(selectedClause.value)) {
            return null;
        }

        return (selectedFilter.value as DropdownFilter)?.options.find((option) => selectedFilter.value.value == option.value)?.label;
    }

    // Handle DateFilter with Between clause (date ranges)
    if (selectedFilter.value.component === 'DateFilter' && selectedClause.value.value === 'between' && Array.isArray(selectedFilter.value.value)) {
        if (selectedFilter.value.value.length === 2) {
            const startDate = selectedFilter.value.value[0]; // Already in YYYY-MM-DD format
            const endDate = selectedFilter.value.value[1]; // Already in YYYY-MM-DD format
            return `${startDate} - ${endDate}`;
        }
    }

    return selectedFilter.value.value;
});

const isDropdownOpen = ref(false);

const focusInputElement = () => {
    if (!isDropdownOpen.value) return;

    nextTick(() => {
        const container = document.querySelector(`#filter-${name}-${selectedFilter.value.component}-${selectedFilter.value.name}`) as HTMLElement;

        if (container) {
            const input = container.querySelector('input, textarea, select') as HTMLElement;
            if (input && !input.closest('[aria-hidden="true"]')) {
                setTimeout(() => {
                    // Focus the input directly instead of using focusOnNth
                    input.focus();
                }, 50);
            }
        }
    });
};

const onOpenDropdown = (opened: boolean) => {
    isDropdownOpen.value = opened;

    if (opened) {
        // Wait for dropdown to be fully open before focusing
        focusInputElement();
    }
};

// Remove onMounted focus to prevent aria-hidden conflicts
// onMounted(() => focusInputElement());
</script>

<template>
    <DropdownMenu class="relative" :defaultOpen="filter.opened" @update:open="onOpenDropdown">
        <div class="flex h-7 items-center overflow-hidden rounded-md border border-border text-sm shadow">
            <DropdownMenuTrigger class="group/filter flex h-full cursor-pointer items-center bg-gray-50 hover:bg-gray-100 hover:text-orange-600">
                <div class="whitespace-nowrap px-2 text-left font-medium">{{ filter.title }}:</div>
                <div class="flex max-w-64 gap-1 truncate pr-2 text-left text-muted-foreground group-hover/filter:text-orange-600">
                    <span class="underline underline-offset-2" v-if="filter.selectedClause">{{ filter.selectedClause?.prefix }}</span>
                    <span v-if="label">{{ label }}</span>
                </div>
            </DropdownMenuTrigger>
            <button @click.prevent="onDeleteFilter" class="h-full bg-gray-50 px-2 py-1 hover:bg-gray-100 hover:text-orange-600">
                <Icon name="X" class="size-3.5" />
            </button>
        </div>

        <DropdownMenuContent class="mt-1 min-w-[300px] space-y-3 bg-gray-50 p-2" align="start">
            <div class="flex items-center gap-2">
                <label>Operator:</label>
                <DropdownMenu class="relative">
                    <DropdownMenuTrigger>
                        <div class="flex items-center gap-1 font-bold text-muted-foreground">
                            <span
                                class="inline-flex items-center gap-1 rounded-md bg-green-50 px-2 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                                >{{ !filter.selectedClause && filter.defaultClause.name == 'Is True' ? 'Not Selected' : selectedClause.name }}</span
                            >
                            <Icon name="ChevronDown" class="w-5" />
                        </div>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent align="start">
                        <DropdownMenuItem v-for="clause in filter.clauses" :key="clause.value" @click="onChangeClause(clause)">
                            <div class="flex items-center gap-1">
                                <span>{{ clause.name }}</span>
                            </div>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <component
                :id="`filter-${name}-${selectedFilter.component}-${selectedFilter.name}`"
                v-if="showComponent"
                v-model="selectedFilter.value"
                @update="onChangeValue"
                :is="filterComponent"
                :filter="selectedFilter"
            ></component>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
