<script setup lang="ts">
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { computed, inject, useAttrs } from 'vue';
import { cn } from '@/lib/utils';
import { getConfig } from '../config';
import { InertiaForm } from '@inertiajs/vue3';
import { useDelegatedAttrs } from '@/components/form/utils';

interface Props {
    id?: string
    name?: string
    label?: string
    error?: string|undefined
    wrapperClass?: string
    labelClass?: string
    inputClass?: string
    errorClass?: string
    precognitive?: boolean
}

const props = defineProps<Props>();

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

const delegatedAttrs = useDelegatedAttrs(attrs,props.precognitive ? ['autocomplete'] : [] )

const handleChange = (event: Event) => {
    event.preventDefault();

    if (form && props.precognitive && props.name) {
        form.validate(props.name);
    }
}
</script>
<template>
    <div :class="cn(getConfig('textfield.wrapperClass'), props.wrapperClass)">
        <Label v-if="label"
               :for="computedId"
               :class="cn(getConfig('textfield.labelClass'), props.labelClass)"
        >{{label}}</Label>
        <Input :class="cn(getConfig('textfield.inputClass'), props.inputClass)"
               v-model="modelValue"
               v-bind="delegatedAttrs"
               :id="computedId"
               @change="handleChange"
        />
        <InputError v-if="error"
                    :class="cn(getConfig('textfield.errorClass'), props.errorClass)"
                    :message="error" />
    </div>
</template>
