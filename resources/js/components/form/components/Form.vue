<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { provide } from 'vue';
import  * as Fields from '@/components/form/components';
import { FormField, FormProps } from '@/types';
import { putConfig } from '@/components/form/config';
import { ConfigType } from '@/components/form/types'

interface Props {
    form: FormProps
}

const props = defineProps<Props>();
provide('form', props.form);

const page = usePage<{
    form: {
        fields: FormField[],
        url: string
    },
    form_defaults: ConfigType
}>();

const emit = defineEmits(['submit']);

const submit = () => {
    props.form.submit()

    emit('submit')
}

const innerHtml = (field: FormField) => {
    if (['Button', 'Submit'].includes(field.component)) {
        return field.name
    }
}

putConfig('textfield', page.props.form_defaults.textfield)
</script>

<template>
    <form @submit.prevent="submit" autocomplete="off" action="" method="post">
        <template v-if="!$slots.default">
            <component
                :is="Fields[field.component]"
                v-bind="field"
                v-for="(field, index) in page.props.form.fields"
                :key="index"
            >{{innerHtml(field)}}</component>
        </template>
        <slot v-else />
    </form>
</template>
