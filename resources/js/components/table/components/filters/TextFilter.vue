<script lang="ts" setup>
import { Input } from '@/components/ui/input';
import { type Filter } from '../../index';
import Search from '../../components/Search.vue';

interface Props {
    filter: Filter;
}

defineProps<Props>();
</script>

<template>
    <Search
        :token="filter.name"
        class="w-full focus-visible:ring-1 focus-visible:ring-offset-1 focus-visible:ring-ring/40 h-8 px-2"
           type="text"
    />
</template>
