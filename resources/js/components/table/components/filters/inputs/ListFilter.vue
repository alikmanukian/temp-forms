<script lang="ts" setup>
import { DropdownFilter, FilterOption } from '@/components/table';
import { ref } from 'vue';
import DebounceInput from '@/components/table/components/DebounceInput.vue';
import { Checkbox } from '@/components/ui/checkbox';
import { AcceptableValue } from 'reka-ui';
import { cn } from '@/lib/utils';
import { ComboboxItem, ComboboxItemIndicator } from '@/components/ui/combobox';
import Icon from '@/components/Icon.vue';

/**
 * This is an alias for the DropdownFilter, but it shows in FilterDropdown popover.
 */

interface Props {
    filter: DropdownFilter;
    modelValue: string|string[]|null;
}

const props = defineProps<Props>()

const model = ref<string>(props.filter.value as string);

const emit = defineEmits<{
    (e: 'update', value: AcceptableValue, clause: string | null): void;
}>();

const selected = ref<FilterOption[]>(
    props.filter.options.filter((option) => props.filter.multiple ? model.value?.includes(option.value) : option.value == model.value)
);

const values = ref<string[]>(selected.value.map((option: FilterOption) => option.value));

const searchInputModel = ref<string>('');

const toggle = (value: string) => {
    if (props.filter.multiple) {
        if (values.value.includes(value)) {
            values.value = values.value.filter((v) => v !== value);
        } else {
            values.value.push(value);
        }
    } else {
        values.value = [value];
    }

    emit('update', values.value, props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol);
}
</script>


<template>
    <div>
        <div class="relative w-full max-w-sm items-center">
            <DebounceInput
                v-model="searchInputModel"
                class="w-full focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:ring-ring/40 h-8 px-2"
                type="text"
                placeholder="Type to search"
                icon="Search"
            />

            <ul class="flex flex-col mt-2 bg-white rounded-md border border-input">
                <li v-for="option in filter.options"
                    :key="option.value"
                    @click="toggle(option.value)"
                    class="flex gap-2 items-center cursor-pointer hover:bg-accent hover:text-accent-foreground rounded-md py-2 px-4"
                >
                    <label class="flex-1 cursor-pointer " :for="`filter-checkbox-${filter.name}-${option.value}`">{{ option.label }}</label>
                    <Icon name="Check" :class="cn('ml-auto h-4 w-4')" v-if="values.includes(option.value)" />
                </li>
            </ul>
        </div>
    </div>
</template>
