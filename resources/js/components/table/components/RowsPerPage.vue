<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import type { VisitOptions } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';
import { useCookies } from '@vueuse/integrations/useCookies';
import type { PaginatedMeta } from '@/types';

const cookies = useCookies();

interface Props {
    meta: PaginatedMeta
    reloadOnly: boolean|string[]
    includeQueryString: boolean
    pageName: string
}

const props = withDefaults(defineProps<Props>(), {
    reloadOnly: false,
    includeQueryString: true,
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

const setPerPage = (value: number) => {
    cookies.set('perPage', value, {path: window.location.pathname, sameSite: 'lax'});

    const params: VisitOptions = {
        data: {page: 1},
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
};
</script>

<template>
    <div class="flex gap-2 items-center">
        <span class="font-medium text-sm whitespace-nowrap">Rows per page</span>

        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="outline" class="flex items-center">
                    <span class="text-sm">{{ props.meta.perPage }}</span>
                    <Icon name="ChevronDown" class="ml-3 w-4 h-4"/>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
                <DropdownMenuCheckboxItem v-for="page in meta.perPageItems"
                                          :checked="props.meta.perPage === page"
                                          @click="setPerPage(page)"
                                          :key="page"
                >
                    {{page}}
                </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
</template>
