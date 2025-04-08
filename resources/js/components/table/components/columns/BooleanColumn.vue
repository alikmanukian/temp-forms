<script lang="ts" setup>
import Icon from '@/components/Icon.vue';
import { computed } from 'vue';

interface Props {
    name: string
    params: Record<string, Record<string, Record<'icon', string>>>
}

const props = defineProps<Props>()

const icon = computed(() => {
    return  props.params.BooleanColumn?.[props.name]?.icon ?? '';
});

console.log(icon.value);
</script>

<template>
    <div class="flex space-x-1 items-center justify-center"><Icon v-if="icon" :name="icon"/><span><slot /></span></div>
</template>
