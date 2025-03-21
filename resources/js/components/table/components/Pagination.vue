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
import { router } from '@inertiajs/vue3';
import { type VisitOptions } from '@inertiajs/core';

interface Props {
    meta: PaginatedMeta
    pageName: string
    reloadOnly?: boolean|string[]
    includeQueryString?: boolean
    stickyPagination?: boolean
    hidePageNumbers?: boolean
    hideArrows?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    includeQueryString: true,
    stickyPagination: false,
    hidePageNumbers: false,
    hideArrows: false,
});

const getQueryParams = (except: string[]|null = null): Record<string, any> => {
    const queryParams = new URLSearchParams(window.location.search);

    if (except) {
        return Object.fromEntries(
            [...queryParams].filter(([key]) => key !== props.pageName)
        );
    }
    return Object.fromEntries([...queryParams]);
}

const changePage = (page: number) => {
    const params: VisitOptions = {
        data: {page: page},
    }

    // set reload only param
    if (props.reloadOnly) {
        params.only = props.reloadOnly as string[]
    }

    // add query string
    if (props.includeQueryString) {
        params.data = { ...params.data, ...getQueryParams([props.pageName]) }
    }

    router.reload(params)
}



</script>

<template>
    <div class="flex gap-6 py-4 mt-4 justify-end border-t border-input items-center"
        :class="{
            'sticky bottom-0 bg-white/75 backdrop-blur backdrop-filter dark:bg-black/25': stickyPagination,
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
                    :disabled="meta.total === 0 || meta.currentPage > meta.lastPage"
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
