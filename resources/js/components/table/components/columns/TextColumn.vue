<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';

interface Image {
    src?: string
    class?: string
    style?: string
    alt?: string
    title?: string
    position?: string
}

interface Props {
    name: string
    params: Record<string, Record<string, Record<'image', Image>>>
}

const props = defineProps<Props>()

const image = computed(() => {
    return  props.params.TextColumn?.[props.name]?.image ?? null;
});
</script>

<template>
    <div class="flex items-center space-x-2">
        <img v-if="image && image.src && image.position === 'start'" v-bind="image"/>
        <span><slot /></span>
        <img v-if="image && image.src && image.position === 'end'" v-bind="image"/>
    </div>
</template>
