<script lang="ts" setup>
import { type Filter } from '../../../index';
import DebounceInput from '../../DebounceInput.vue';
import { ref, watch } from 'vue';

interface Props {
    filter: Filter;
    modelValue: string|null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', value: string, clause: string|null): void;
}>();

const search = (value: string) => {
    emit('update', value, props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol);
}

const model = ref<string>(props.filter.value as string);

watch(() => props.modelValue, (newValue) => {
    model.value = newValue as string;
});

</script>

<template>
    <DebounceInput
        v-model="model"
        class="w-full focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:ring-ring/40 h-8 px-2"
        type="text"
        @update="search"
    />
</template>
