<script setup lang="ts">
import DebounceInput from '@/components/table/components/DebounceInput.vue';
import { inject, onMounted, computed, nextTick } from 'vue';
import { useFilters } from '@/components/table/utils/filterable';
import type { Filter } from '@/components/table';
import Icon from '@/components/Icon.vue';
import { focusOnNth } from 'usemods'

interface Props {
    searchable: string[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', value: string): void;
}>()

const pageName = inject('pageName') as string;
const name = inject('name') as string;
const filters = inject('filters') as Record<string, Filter>;

const { searchBy } = useFilters(pageName, name, filters);

const searchString = defineModel<string>();

const resetSearch = () => {
    searchBy('search', '', '');
    emit('update', '');
    if (inputContainer.value) {
        focusOnNth(inputContainer.value, 0);
    }
};

const onSearch = (value: string) => {
    searchBy('search', value, '')
    emit('update', value);
}

onMounted(() => {
    if (inputContainer.value) {
        nextTick(() => focusOnNth(inputContainer.value, 0))
    }
})

const inputContainer = computed(() => document.querySelector(`#search-input-${name}`) as HTMLInputElement)

const searchableFields = computed(() => {
    return props.searchable.map((field) => filters[field].title).join(', ');
})
</script>
<template>
    <div v-if="searchable.length > 0" class="relative">
        <DebounceInput
            :id="`search-input-${name}`"
            v-if="searchable"
            v-model="searchString as string"
            @update="onSearch"
            class="!border-none pr-10 ring-1 ring-input"
            :class="{ ' text-green-700 !ring-green-600': searchString }"
            v-bind="$attrs"
        />

        <p class="text-gray-500/70 text-sm mt-1.5 pl-1">Search in following fields: <span class="medium text-gray-500 lowercase">{{ searchableFields }}</span></p>

        <button v-if="searchString"
                class="absolute right-0 top-0 flex size-10 items-center justify-center border-none outline-none"
                @click="resetSearch"
        >
            <span class="flex transition group size-6 items-center justify-center hover:bg-green-50 hover:scale-110 rounded-lg">
                <Icon name="X" class="size-4 text-green-700 transition group-hover:scale-110" />
            </span>
        </button>
    </div>
</template>
