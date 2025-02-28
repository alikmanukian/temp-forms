<script setup lang="ts">

interface FormField {
    name: string;
    component: string;
    required?: boolean;
    type?: 'text' | 'email' | 'password' | 'checkbox' | 'radio';
}

interface FormProps extends InertiaForm<any>{
    fields: FormField[]
}

import { type InertiaForm } from '@inertiajs/vue3';
import { provide } from 'vue';

interface Props {
    form: FormProps
}

const props = defineProps<Props>();
provide('form', props.form);

const emit = defineEmits(['submit']);

const submit = () => {
    if (props.form.submitForm) {
        props.form.submitForm();
    }

    emit('submit')
}
</script>

<template>
    <form @submit.prevent="submit" autocomplete="off">
        <slot />
    </form>
</template>
