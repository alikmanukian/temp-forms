<script setup lang="ts">
import { SidebarInset, useSidebar } from '@/components/ui/sidebar';
import { computed } from 'vue';
import { cn } from '@/lib/utils';

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>();
const className = computed(() => props.class);

const { state, isMobile } = useSidebar();
const mainContainerMaxWidth = computed(() => {
        if (isMobile.value) {
            return 'max-w-[100vw]';
        }

        return state.value === 'collapsed'
            ? 'max-w-[calc(100vw-var(--sidebar-width-icon))]'
            : 'max-w-[calc(100vw-var(--sidebar-width))]'
    }
);
</script>

<template>
    <SidebarInset v-if="props.variant === 'sidebar'" :class="cn([className, mainContainerMaxWidth])">
        <slot />
    </SidebarInset>
    <main v-else class="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-4 rounded-xl" :class="className">
        <slot />
    </main>
</template>
