<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
    DropdownMenuItem
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import type { Filter } from '@/components/table';

interface Props {
    filters: Record<string, Filter>;
}

defineProps<Props>()

const emit = defineEmits<{
    (e: 'update', name: string): void;
}>()

const onAddFilter = (name: string) => {
    emit('update', name);
}
</script>

<template>
    <DropdownMenu class="relative">
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="flex items-center">
                <Icon name="Filter" class="mr-1 w-4 h-4"/>
                <span class="text-sm">Filters</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
            <DropdownMenuLabel>Add filter</DropdownMenuLabel>
            <DropdownMenuSeparator class="bg-gray-300" />

                <DropdownMenuItem v-for="filter in filters"
                    :key="filter.name" @click="onAddFilter(filter.name)"
                >
                    <Icon name="Plus" class="mr-1 !size-3" /><span>{{filter.title}}</span>
                </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>

