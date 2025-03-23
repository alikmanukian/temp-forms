<script lang="ts" setup>
import type { TableHeader } from '@/components/table';
import Icon from '@/components/Icon.vue';
import {
    DropdownMenu,
    DropdownMenuItem,
    DropdownMenuContent,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';
import DraggableList from '@/components/table/components/DraggableList.vue';
import { Button } from '@/components/ui/button';
import { computed } from 'vue';
import { cn } from '@/lib/utils';
import { useToggleColumns } from '@/components/table/utils/toggleColumns';

interface Props {
    column: TableHeader
}

interface Callback {
    (): void
}

interface ListItem {
    name: string
    title: string
    icon: string
    handler: Callback
}

const props = defineProps<Props>()

const { toggleColumn } = useToggleColumns([])

const hideColumn = () => {
    toggleColumn(props.column.name)
}

const list : ListItem[] = [
    {
        name: 'orderAsc',
        title: 'Asc',
        icon: 'ArrowUpNarrowWide',
        handler: () => {}
    },
    {
        name: 'orderDesc',
        title: 'Desc',
        icon: 'ArrowDownWideNarrow',
        handler: () => {}
    },
    {
        name: 'hide',
        title: 'Hide',
        icon: 'EyeOff',
        handler: hideColumn
    },
    {
        name: 'lock',
        title: 'Freeze',
        icon: 'LockKeyhole',
        handler: hideColumn
    }
]

const hasButton = computed(() => props.column.options.stickable)
</script>
<template>
    <div class="flex items-center h-full" :class="column.options.headerAlignment">
        <div class="truncate h-full flex items-center"
             :class="cn([column.options.headerClass, hasButton ? 'px-2' : 'px-4'])"
        >
            <template v-if="!hasButton">{{ column.header }}</template>
            <template v-else>
                <DropdownMenu class="relative">
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="flex items-center px-2 py-1 shadow-none border-none bg-inherit text-muted-foreground hover:text-muted-foreground hover:bg-background">
                            <span class="text-sm">{{ column.header }}</span>
                            <Icon name="ChevronsUpDown" class="w-4 h-4"/>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DraggableList #default="{ item }: { item: ListItem }"
                                       :list="list"
                                       class="space-y-1"
                        >
                            <DropdownMenuItem :key="item.name" @click="item.handler">
                                <Icon :name="item.icon" class="w-4 h-4"/>
                                <span class="text-sm">{{ item.title }}</span>
                            </DropdownMenuItem>
                        </DraggableList>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
        </div>
    </div>
</template>
