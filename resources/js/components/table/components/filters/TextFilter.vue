<script lang="ts" setup>
import { type Filter } from '../../index';
import Search from '../../components/Search.vue';
import { ref } from 'vue';

interface Props {
    filter: Filter;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', value: string): void;
}>();

const search = (value: string) => {
    emit('update', value)
}

const model = ref<string>(props.filter.value);

</script>

<template>
    <Search
        v-model="model"
        class="w-full focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:ring-ring/40 h-8 px-2"
        type="text"
        @update="search"
    />
</template>
