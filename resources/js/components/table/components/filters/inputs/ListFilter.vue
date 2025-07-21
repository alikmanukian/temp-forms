<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { DropdownFilter, FilterOption } from '@/components/table';
import DebounceInput from '@/components/table/components/DebounceInput.vue';
import { cn } from '@/lib/utils';
import { AcceptableValue } from 'reka-ui';
import { computed, ref, watch } from 'vue';

/**
 * This is an alias for the DropdownFilter, but it shows in FilterDropdown popover.
 */

interface Props {
    filter: DropdownFilter;
    modelValue: string | string[] | null;
}

const props = defineProps<Props>();

const model = ref<string | string[]>('');

const emit = defineEmits<{
    (e: 'update', value: AcceptableValue, clause: string | null): void;
}>();

const selected = ref<FilterOption[]>(
    props.filter.options.filter((option) => (props.filter.multiple ? model.value?.includes(option.value) : option.value == model.value)),
);

const value = ref<string[]>(selected.value.map((option: FilterOption) => option.value));

const setModelValue = (newValue: string | string[] | null) => {
    model.value = newValue ? newValue : props.filter.multiple ? [] : '';

    selected.value = props.filter.options.filter((option) =>
        props.filter.multiple ? model.value?.includes(option.value) : option.value == model.value,
    );
    value.value = selected.value.map((option: FilterOption) => option.value);
};

setModelValue(props.modelValue);

const searchInputModel = ref<string>('');

const toggle = (newValue: string) => {
    if (props.filter.multiple) {
        if (value.value.includes(newValue)) {
            value.value = value.value.filter((v) => v !== newValue);
        } else {
            value.value.push(newValue);
        }
    } else {
        value.value = [newValue];
    }

    emit('update', value.value, props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol);
};

watch(
    () => props.modelValue,
    (newValue: string | string[] | null) => {
        setModelValue(newValue);
    },
);

const options = computed(() => props.filter.options.filter((option) => option.label.toLowerCase().includes(searchInputModel.value.toLowerCase())));
</script>

<template>
    <div>
        <div class="relative w-full max-w-sm items-center">
            <DebounceInput
                v-model="searchInputModel"
                class="h-8 w-full px-2 focus-visible:ring-1 focus-visible:ring-ring/40 focus-visible:ring-offset-1"
                type="text"
                placeholder="Type to search"
                icon="Search"
            />

            <ul class="mt-2 flex flex-col rounded-md border border-input bg-white">
                <li
                    v-for="option in options"
                    :key="option.value"
                    @click="toggle(option.value)"
                    class="flex cursor-pointer items-center gap-2 rounded-md px-4 py-2 hover:bg-accent hover:text-accent-foreground"
                >
                    <label class="flex-1 cursor-pointer" :for="`filter-checkbox-${filter.name}-${option.value}`">{{ option.label }}</label>
                    <Icon name="Check" :class="cn('ml-auto h-4 w-4')" v-if="value.includes(option.value)" />
                </li>
            </ul>
        </div>
    </div>
</template>
