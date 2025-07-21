<script lang="ts" setup>
import { Button } from '@/components/form';
import { Calendar } from '@/components/ui/calendar';

import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CalendarDate, DateFormatter, getLocalTimeZone, parseDate, today, type DateValue } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { toDate } from 'reka-ui/date';
import { computed, ref, watch } from 'vue';
import { type Filter } from '../../../index';

interface Props {
    filter: Filter;
    modelValue: string | null;
    inHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    inHeader: false,
});

const emit = defineEmits<{
    (e: 'update', value: string, clause: string | null): void;
}>();

const isDefaultClause = ref<boolean>(props.filter.selectedClause?.value === props.filter.defaultClause.value);

const model = ref<string | null>(null);

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

// Convert string date to CalendarDate object
const dateValue = computed(() => {
    if (!model.value) return undefined;
    try {
        return parseDate(model.value);
    } catch {
        return undefined;
    }
});

const setModelValue = (value: string | null) => {
    if (!props.inHeader || isDefaultClause.value) {
        model.value = value;
    } else {
        model.value = null;
    }
};

setModelValue(props.filter.value);

const search = (value: string | null) => {
    emit(
        'update',
        value || '',
        props.inHeader
            ? props.filter.defaultClause.searchSymbol
            : (props.filter.selectedClause?.searchSymbol ?? props.filter.defaultClause.searchSymbol),
    );
};

const onDateSelect = (date: DateValue | undefined) => {
    if (date) {
        // Convert CalendarDate to ISO string format for filtering
        const dateString = date.toString(); // This gives YYYY-MM-DD format
        model.value = dateString;
        search(dateString);
    } else {
        model.value = null;
        search(null);
    }
};

watch(
    () => props.modelValue,
    (newValue) => {
        setModelValue(newValue);
    },
);

watch(
    () => props.filter.selectedClause,
    (newValue) => {
        isDefaultClause.value = newValue?.value === props.filter.defaultClause.value;
        setModelValue(props.filter.value);
    },
);
</script>

<template>
    <!-- Show calendar directly in FilterDropdown (when not inHeader) -->
    <div v-if="!inHeader">
        <Calendar
            :model-value="dateValue"
            calendar-label="Select date"
            initial-focus
            :min-value="new CalendarDate(1900, 1, 1)"
            :max-value="today(getLocalTimeZone())"
            @update:model-value="onDateSelect"
        />
    </div>

    <!-- Show button with popover calendar in table header (when inHeader) -->
    <Popover v-else>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('h-8 w-full px-2 font-normal', !dateValue && 'text-muted-foreground')">
                <span>{{ dateValue ? df.format(toDate(dateValue)) : 'Pick a date' }}</span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar
                :model-value="dateValue"
                calendar-label="Select date"
                initial-focus
                :min-value="new CalendarDate(1900, 1, 1)"
                :max-value="today(getLocalTimeZone())"
                @update:model-value="onDateSelect"
            />
        </PopoverContent>
    </Popover>
</template>
