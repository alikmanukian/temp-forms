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

interface Props {
    filter: DropdownFilter;
}

const props = defineProps<Props>()

const model = ref<string>(props.filter.value as string);

const selected = ref<FilterOption[]>(
    props.filter.options.filter((option) => props.filter.multiple ? model.value?.includes(option.value) : option.value == model.value)
);

const value = ref<string[]>(selected.value.map((option: FilterOption) => option.value));

const emit = defineEmits<{
    (e: 'update', value: string|null, clause: string | null): void;
}>();

const placeholder = ` `;

watch(() => value.value, (newValue) => {
    selected.value = props.filter.options.filter((item) => props.filter.multiple ? newValue.includes(item.value) : newValue == item.value);
    emit('update',
        props.filter.multiple
            ? selected.value.map((item: FilterOption) => item.value)
            : selected.value[0]?.value,
        props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol
    );
})

const label = computed(() => {
    if (props.filter.multiple) {
        switch(selected.value.length) {
            case 0: return placeholder
            case 1: return selected.value[0].label
            default: return selected.value.length + ' items'
        }
    }

    return selected.value[0]?.label ?? placeholder
})
</script>

<template>
    <Combobox v-model="value" by="label" :multiple="filter.multiple" class="h-8">
        <ComboboxAnchor as-child>
            <ComboboxTrigger as-child>
                <Button variant="outline" class="justify-between w-full px-2 h-8">
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
