<script lang="ts" setup>
interface Props {
    name: string
    params: any
}

defineProps<Props>()
</script>

<template>
    <slot />
</template>
