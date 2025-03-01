<script setup lang="ts">
import { InertiaForm } from '@inertiajs/vue3';
import { computed, inject, reactive, useAttrs, defineAsyncComponent } from 'vue';
import { cn } from '@/lib/utils';
import { getConfig } from '../config';
import { useDelegatedAttrs } from '../utils';
import { TextFieldType } from '../types';

interface Props extends TextFieldType {
    id?: string
    name?: string
    label?: string
    error?: string|undefined
    precognitive?: boolean
}

const props = defineProps<Props>();

const components = reactive<{
    label: ReturnType<typeof defineAsyncComponent>,
    input: ReturnType<typeof defineAsyncComponent>,
    error: ReturnType<typeof defineAsyncComponent>,
}>({
    label: defineAsyncComponent(() => import(/* @vite-ignore */props.labelComponent)),
    input: defineAsyncComponent(() => import(/* @vite-ignore */props.inputComponent)),
    error: defineAsyncComponent(() => import(/* @vite-ignore */props.errorComponent))
})

const form = inject('form') as InertiaForm<any> || null;

const model = defineModel<string|number>()

// Compute `modelValue` dynamically
const modelValue = computed({
    get: () => {
        if (model.value !== undefined) {
            return model.value; // Direct v-model binding
        }
        return form && props.name ? form.data()[props.name] : undefined; // Fallback to form[name]
    },
    set: (value) => {
        if (model.value !== undefined) {
            model.value = value; // Update if v-model is used
        } else if (form && props.name) {
            form[props.name] = value; // Update form[name] if name is provided
        }
    }
});

const computedId = computed(() => props.id ?? `input-${Math.random().toString(36).slice(2, 12)}`);

const error = computed(() => form && props.name ? form.errors[props.name] : props.error);

const attrs = useAttrs()

const disallowedAttributes = ['component', 'value']

if (props.precognitive) {
    disallowedAttributes.push('autocomplete')
}

const delegatedAttrs = useDelegatedAttrs(attrs, disallowedAttributes)

const handleChange = (event: Event) => {
    event.preventDefault();

    if (form && props.precognitive && props.name) {
        form.validate(props.name);
    }
}

const labelSuffix = computed(() => attrs.required ? ' *' : '')
</script>

<template>
    <div :class="cn(getConfig('textfield.wrapperClass'), props.wrapperClass)">
        <component :is="components.label" v-if="label"
               :for="computedId"
               :class="cn(getConfig('textfield.labelClass'), props.labelClass)"
        >{{label}}{{labelSuffix}}</component>
        <component :is="components.input" :class="cn(getConfig('textfield.inputClass'), props.inputClass)"
               v-model="modelValue"
               v-bind="delegatedAttrs"
               :id="computedId"
               @change="handleChange"
        />
        <component :is="components.error" v-if="error"
                    :class="cn(getConfig('textfield.errorClass'), props.errorClass)"
                    :message="error" />
    </div>
</template>
