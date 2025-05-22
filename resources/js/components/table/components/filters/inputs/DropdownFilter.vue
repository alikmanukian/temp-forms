<script lang="ts" setup>
import { type DropdownFilter, FilterOption } from '../../../index';
import { computed, ref, watch } from 'vue';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';
import {
    Combobox,
    ComboboxAnchor,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxList,
    ComboboxTrigger,
} from '@/components/ui/combobox';
import Icon from '@/components/Icon.vue';
import { AcceptableValue } from 'reka-ui';

interface Props {
    filter: DropdownFilter;
    modelValue: string | string[] | number | null;
    inHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    inHeader: false,
});

const emit = defineEmits<{
    (e: 'update', value: AcceptableValue, clause: string | null): void;
}>();

const isDefaultClause = ref<boolean>(props.filter.selectedClause?.value === props.filter.defaultClause.value);

const model = ref<string | string[] | number | null>(null);

const selected = ref<FilterOption[]>(
    props.filter.options.filter((option) => (props.filter.multiple && Array.isArray(model.value) ? model.value?.includes(option.value) : option.value == model.value)),
);

const value = ref<string[]>(selected.value.map((option: FilterOption) => option.value));

const setModelValue = (newValue: string | string[] | number | null) => {
    if (props.filter.name === 'is_verified') {
        console.log('setModelValue', { 'inHeader': props.inHeader, newValue });
    }
    model.value = newValue ? newValue : props.filter.multiple ? [] : '';

    selected.value = props.filter.options.filter((option) =>
        props.filter.multiple && Array.isArray(model.value) ? model.value?.includes(option.value) : option.value === model.value,
    );
    value.value = selected.value.map((option: FilterOption) => option.value);
    if (props.filter.name === 'is_verified') {
        console.log('selected', {
            name: props.filter.name,
            value: value.value,
            options: props.filter.options,
            model: model.value,
        });
    }
};

setModelValue(props.modelValue);

const placeholder = ` `;

const label = computed(() => {
    if (props.filter.multiple) {
        switch (selected.value.length) {
            case 0:
                return placeholder;
            case 1:
                return selected.value[0].label;
            default:
                return selected.value.length + ' items';
        }
    }

    return selected.value[0]?.label ?? placeholder;
});

const search = (value: AcceptableValue) => {
    emit(
        'update',
        value,
        props.inHeader
            ? props.filter.defaultClause.searchSymbol
            : (props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol),
    );
};

watch(
    () => props.modelValue,
    (newValue: string | string[] | number | null) => {
        setModelValue(newValue);
    },
);

watch(
    () => props.filter.selectedClause,
    (newValue) => {
        isDefaultClause.value = newValue?.value === props.filter.defaultClause.value;

        setModelValue(props.filter.value);
    },
);
</script>

<template>
    <Combobox v-model="value" by="label" :multiple="filter.multiple" class="h-8" @update:modelValue="search">
        <ComboboxAnchor as-child>
            <ComboboxTrigger as-child>
                <Button variant="outline" class="h-8 w-full justify-between px-2">
                    <span class="flex-1 text-left">{{ label }}</span>

                    <Icon name="ChevronsUpDown" class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </ComboboxTrigger>
        </ComboboxAnchor>

        <ComboboxList>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput class="h-10 rounded-none border-0 border-b pl-9 focus-visible:ring-0" placeholder="Type to search" />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Icon name="Search" class="size-4 text-muted-foreground" />
                </span>
            </div>

            <ComboboxEmpty> No items found. </ComboboxEmpty>

            <ComboboxGroup>
                <ComboboxItem v-for="option in filter.options" :key="option.value" :value="option.value">
                    {{ option.label }}

                    <ComboboxItemIndicator>
                        <Icon name="Check" :class="cn('ml-auto h-4 w-4')" v-if="value.includes(option.value)" />
                    </ComboboxItemIndicator>
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>
