<script lang="ts" setup>
defineProps<{
    meta: {
        current_page: number
        per_page: number
        total: number
        last_page: number
    }
}>()

</script>

<template>
    <div>Pagination</div>
</template>
