<script lang="ts" setup>
import { DropdownFilter as DropdownFilterType } from '@/components/table';
import DropdownFilter from './DropdownFilter.vue'
import { ref, watch } from 'vue';
import { AcceptableValue } from 'reka-ui';

interface Props {
    filter: DropdownFilterType;
    modelValue: string|string[]|null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', value: AcceptableValue, clause: string | null): void;
}>();

const setModelValue = (newValue: any) => {
    model.value = newValue === null ? newValue : (newValue ? 1 : 0);
}

const model=ref<any>(null)

setModelValue(props.modelValue);

const onUpdate = (value: AcceptableValue) => {
    if (value == props.modelValue) {
        // value not changed (unselected)
        emit('update', null, '');
    } else {
        emit(
            'update',
            value ? 'true' : 'false',
            ''
        );
    }

}

watch(() => props.modelValue, (newValue: any) => {
    setModelValue(newValue);
});
</script>


<template>
    <DropdownFilter :filter
                    v-model="model"
                    @update="onUpdate"
    ></DropdownFilter>
</template>
