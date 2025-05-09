<script lang="ts" setup>
import {
    Button,
} from '@/components/ui/button'

import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination'

import type { PaginatedMeta } from '../index';

interface Props {
    meta: PaginatedMeta
    stickyPagination?: boolean
    hidePageNumbers?: boolean
    hideArrows?: boolean
    disabled?: boolean
}

withDefaults(defineProps<Props>(), {
    stickyPagination: false,
    hidePageNumbers: false,
    hideArrows: false,
    disabled: false,
});

const emit = defineEmits<{
    (e: 'update', page: number): void;
}>();

const changePage = (page: number) => {
    emit('update', page)
}
</script>

<template>
    <div class="flex gap-6 py-4 mt-4 justify-end border-t border-input items-center"
        :class="{
            'sticky bottom-0 bg-white/75 backdrop-blur backdrop-filter dark:bg-black/25 z-10': stickyPagination,
        }"
    >
        <div class="font-medium text-sm whitespace-nowrap">Page <span class="font-bold">{{ meta.currentPage }}</span> of <span class="font-bold">{{ meta.lastPage }}</span></div>

        <Pagination v-slot="{ page }"
                    :items-per-page="meta.perPage"
                    :total="meta.total"
                    :sibling-count="1"
                    :default-page="1"
                    :page="meta.currentPage"
                    @update:page="changePage"
                    :disabled="disabled || meta.total === 0 || meta.currentPage > meta.lastPage"
                    show-edges
        >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
                <PaginationFirst v-if="!hideArrows" />
                <PaginationPrev v-if="!hideArrows" />

                <template v-if="!hidePageNumbers">
                    <template v-for="(item, index) in items">
                        <PaginationListItem v-if="item.type === 'page'"
                                            :key="index"
                                            :value="item.value"
                                            as-child
                                            class="hidden @lg:inline-flex"
                        >
                            <Button class="w-9 h-9 p-0" :variant="item.value === page ? 'default' : 'outline'">
                                {{ item.value }}
                            </Button>
                        </PaginationListItem>
                        <PaginationEllipsis v-else :key="item.type" :index="index" class="hidden @lg:flex" />
                    </template>
                </template>

                <PaginationNext v-if="!hideArrows" />
                <PaginationLast v-if="!hideArrows" />
            </PaginationList>
        </Pagination>
    </div>
</template>
