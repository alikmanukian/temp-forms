<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator } from '@/components/ui/combobox';
import { DropdownFilter, FilterOption } from '@/components/table';
import Icon from '@/components/Icon.vue';
import { ref } from 'vue';
import DebounceInput from '@/components/table/components/DebounceInput.vue';

interface Props {
    filter: DropdownFilter;
}

const props = defineProps<Props>()

const model = ref<string>(props.filter.value as string);

const selected = ref<FilterOption[]>(
    props.filter.options.filter((option) => props.filter.multiple ? model.value?.includes(option.value) : option.value == model.value)
);

const value = ref<string[]>(selected.value.map((option: FilterOption) => option.value));

</script>


<template>
    <div>
        <div class="relative w-full max-w-sm items-center">
            <DebounceInput
                v-model="model"
                class="w-full focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:ring-ring/40 h-8 px-2"
                type="text"
                placeholder="Type to search"
                icon="Search"
            />
        </div>
    </div>
</template>
