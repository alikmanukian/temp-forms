<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import {
    DropdownMenu,
    DropdownMenuItem,
    DropdownMenuContent,
    DropdownMenuTrigger,
    DropdownMenuSeparator
} from '@/components/ui/dropdown-menu';
import DraggableList from '../components/DraggableList.vue';
import { Button } from '@/components/ui/button';
import { computed, inject } from 'vue';
import { cn } from '@/lib/utils';
import { useStickableColumns } from '../utils/stickable';
import type { TableHeader } from '../index';
import { useToggleColumns } from '@/components/table/utils/toggleable';

interface Props {
    column: TableHeader
}

interface Callback {
    (): void
}

interface ListItem {
    type: 'divider'|'action'
    name?: string
    title?: string
    icon?: string
    handler?: Callback
}

const props = defineProps<Props>()
const pageName = inject<string>('pageName') ?? 'page'

const { stickColumn } = useStickableColumns(pageName)
const { toggleColumn } = useToggleColumns(pageName)

const list = computed(() => {
    const items : ListItem[] = [];

    if (props.column.options.sortable) {
        items.push({
            type: 'action',
            name: 'orderAsc',
            title: 'Asc',
            icon: 'ArrowUpNarrowWide',
            handler: () => {}
        })

        items.push({
            type: 'action',
            name: 'orderDesc',
            title: 'Desc',
            icon: 'ArrowDownWideNarrow',
            handler: () => {}
        })
    }


    if (props.column.options.toggleable) {
        if (items.length > 0 && items[items.length - 1].name == 'orderDesc') {
            items.push({
                type: 'divider'
            })
        }

        items.push({
            type: 'action',
            name: 'hide',
            title: 'Hide',
            icon: 'EyeOff',
            handler: () => toggleColumn(props.column.name)
        });
    }

    if (props.column.options.stickable) {
        if (items.length > 0 && items[items.length - 1].name == 'orderDesc') {
            items.push({
                type: 'divider'
            })
        }

        if (props.column.sticked) {
            items.push({
                type: 'action',
                name: 'unstick',
                title: 'Unstick',
                icon: 'Unlock',
                handler: () => stickColumn(props.column.name)
            })
        } else {
            items.push({
                type: 'action',
                name: 'stick',
                title: 'Stick',
                icon: 'Lock',
                handler: () => stickColumn(props.column.name)
            });
        }
    }
    return items;
})

const hasButton = computed(() => list.value.length > 0)
</script>
<template>
    <div ref="headerButton" data-name="header-button-container" class="flex items-center h-full relative" :class="column.options.headerAlignment">
        <div data-name="header-button" class="h-full flex items-center"
             :class="cn([column.options.headerClass, hasButton ? 'px-2' : 'px-4'])"
        >
            <template v-if="!hasButton">{{ column.header }}</template>
            <template v-else>
                <DropdownMenu class="relative">
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="flex items-center px-2 py-1 shadow-none border-none bg-inherit text-muted-foreground hover:text-muted-foreground hover:bg-background">
                            <Icon name="Lock" class="!w-3.5 !h-3.5" v-if="column.sticked" />
                            <span class="text-sm">{{ column.header }}</span>
                            <Icon name="ChevronsUpDown" class="w-4 h-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent>
                        <DraggableList #default="{ item }: { item: ListItem }"
                                       :list="list"
                                       class="space-y-1"
                        >
                            <DropdownMenuSeparator v-if="item.type === 'divider'"/>
                            <DropdownMenuItem v-else :key="item.name!" @click="item.handler!">
                                <Icon :name="item.icon!" class="w-4 h-4"/>
                                <span class="text-sm">{{ item.title }}</span>
                            </DropdownMenuItem>
                        </DraggableList>
                    </DropdownMenuContent>
                </DropdownMenu>
            </template>
        </div>
    </div>
</template>
