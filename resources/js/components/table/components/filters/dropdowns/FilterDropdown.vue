<script setup lang="ts">
import Icon from '@/components/Icon.vue';
import type { Filter } from '@/components/table';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem
} from '@/components/ui/dropdown-menu';
import { TextFilter } from '@/components/table/components/filters/inputs';

interface Props {
    filter: Filter;
}

defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', name: string, value: string): void;
}>();

const clear = (name: string, value: string) => {
    emit('update', name, value);
};
</script>

<template>
    <DropdownMenu class="relative">
        <div class="flex h-7 items-center overflow-hidden rounded-md text-sm shadow">
            <DropdownMenuTrigger class="group/filter flex h-full cursor-pointer items-center bg-gray-50 hover:bg-gray-100 hover:text-orange-600">
                <span class="px-2 text-left font-medium">{{ filter.title }}</span>
                <span class="flex items-center px-1 text-center text-lg leading-none">
                    <Icon v-if="filter.selectedClause" :name="filter.selectedClause as string" class="size-4" />
                </span>
                <span class="px-2 text-left text-muted-foreground group-hover/filter:text-orange-600">{{ filter.value }}</span>
            </DropdownMenuTrigger>
            <button @click.prevent="clear(filter.name, '')" class="h-full bg-gray-50 px-2 py-1 hover:bg-gray-100 hover:text-orange-600">
                <Icon name="X" class="size-3.5" />
            </button>
        </div>

        <DropdownMenuContent class="min-w-[300px] bg-gray-50 p-2 space-y-3 mt-1" align="start">
            <div class="flex gap-2">
                <label class="font-medium">Operator:</label>
                <DropdownMenu class="relative">
                    <DropdownMenuTrigger>
                        <div class="flex gap-1 items-center text-muted-foreground">
                            <Icon v-if="filter.selectedClause" :name="filter.selectedClause as string" class="size-4" />
                            <span>Contains</span>
                            <Icon name="ChevronDown" class="w-5" />
                        </div>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start">
                        <DropdownMenuItem>Contains</DropdownMenuItem>
                        <DropdownMenuItem>Does Not Contain</DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <TextFilter :filter />
        </DropdownMenuContent>
    </DropdownMenu>
</template>
