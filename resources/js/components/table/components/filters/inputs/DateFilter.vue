<script lang="ts" setup>
import { type Filter } from '../../../index';
import DebounceInput from '../../DebounceInput.vue';
import { ref, watch } from 'vue';

interface Props {
    filter: Filter;
    modelValue: string|null;
    inHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    inHeader: false
});

const emit = defineEmits<{
    (e: 'update', value: string, clause: string|null): void;
}>();

const setModelValue = (value: string|null) => {
    if (!props.inHeader || isDefaultClause.value) {
        model.value = value as string;
    } else {
        model.value = '';
    }
}

const isDefaultClause = ref<boolean>(props.filter.selectedClause?.value === props.filter.defaultClause.value);

const model = ref<string>('');

setModelValue(props.filter.value);

const search = (value: string) => {
    emit(
        'update',
        value,
        props.inHeader
            ? props.filter.defaultClause.searchSymbol
            : props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol
    );
}

watch(() => props.modelValue, (newValue) => {
    setModelValue(newValue);
});

watch(() => props.filter.selectedClause, (newValue) => {
    isDefaultClause.value = newValue?.value === props.filter.defaultClause.value;

    setModelValue(props.filter.value);
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
