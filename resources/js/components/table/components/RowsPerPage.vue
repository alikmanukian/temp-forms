<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuTrigger
} from '@/components/ui/dropdown-menu';

import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useCookies } from '@vueuse/integrations/useCookies';
import type { PaginatedMeta } from '../index';
import { inject } from 'vue';

const cookies = useCookies();

const name = inject<string>('name') as string

interface Props {
    meta: PaginatedMeta
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update', page: number): void;
}>();

const setPerPage = (value: number) => {
    cookies.set('perPage_' + name, value, {path: window.location.pathname, sameSite: 'lax'});

    emit('update', 1);
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
                <DropdownMenuCheckboxItem v-for="page in meta.perPageOptions"
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
