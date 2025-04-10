<script lang="ts" setup>
import type { Icon as TypeIcon } from './columns';
import Icon from '@/components/Icon.vue';

defineProps<{
    icon?: TypeIcon|null
}>()

</script>

<template>
    <Icon v-if="icon && icon.name" v-bind="icon" />
</template>
