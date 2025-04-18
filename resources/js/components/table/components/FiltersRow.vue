<script setup lang="ts">
import type { Filter } from '../index';
import { inject, watch, ref } from 'vue';
import { useFilters } from '@/components/table/utils/filterable';
import { usePage,  } from '@inertiajs/vue3';
interface Props {
    filters: Filter[]
}
const props = defineProps<Props>()

const pageName = inject<string>('pageName') as string;

const page = usePage<{
    query: any;
}>();

const { getFilterParam } = useFilters(pageName)

interface AppliedFilter {
    name: string
    value: string
    options: Filter
}

const appliedFilters = ref<AppliedFilter[]>([]);

watch(() => page.props.query, (value) => {
    appliedFilters.value = props
        .filters
        .map((filter) => {
            const param = getFilterParam(filter.name);
            const v = param
                .split('.')
                .reduce((acc, key) => {
                    if (acc && acc.hasOwnProperty(key)) {
                        return acc[key]
                    }

                    return null
                }, value);

            if (v === null) {
                return null;
            }

            return {
                name: filter.name,
                value: v,
                options: filter
            }
        })
        .filter((filter) => filter !== null);

}, { immediate: true });

const emit = defineEmits<{
    (e: 'update', name: string, value: string): void;
}>();

const clear = (name: string, value: string) => {
    emit('update', name, value)
}

</script>

<template>
    <div class="p-4">
        <div v-for="filter in appliedFilters" :key="filter.name">
            {{ filter.name}}: {{filter.value}} <button @click.prevent="clear(filter.name, '')">&times;</button>
        </div>
    </div>
</template>
