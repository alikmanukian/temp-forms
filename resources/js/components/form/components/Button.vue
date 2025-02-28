<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { inject } from 'vue';
import { InertiaForm } from '@inertiajs/vue3';

const form = inject('form') as InertiaForm<any> || null;
</script>

<template>
    <Button :disabled="form && form.processing"><slot /></Button>
</template>
