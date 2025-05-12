<script setup lang="ts">
import type { DropdownFilter as DropdownFilterType } from '@/components/table';
import DropdownFilter from './DropdownFilter.vue'
import { ref } from 'vue';

interface Props {
    filter: DropdownFilterType;
    modelValue: string|string[]|null;
    inHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    inHeader: false
});

const model=ref<string|string[]|null>(props.modelValue)
</script>


<template>
    <DropdownFilter :filter v-model="model" :inHeader></DropdownFilter>
</template>
