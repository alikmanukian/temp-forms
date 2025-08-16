<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';

import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { cn } from '@/lib/utils';
import { CalendarDate, DateFormatter, getLocalTimeZone, parseDate, today, type DateValue, type DateRange } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { toDate } from 'reka-ui/date';
import { computed, ref, watch } from 'vue';
import { type Filter } from '../../../index';
import { RangeCalendar } from '@/components/ui/range-calendar';

interface Props {
    filter: Filter;
    modelValue: string | string[] | null;
    inHeader?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    inHeader: false,
});

const emit = defineEmits<{
    (e: 'update', value: string, clause: string | null): void;
}>();

const isDefaultClause = ref<boolean>(props.filter.selectedClause?.value === props.filter.defaultClause.value);

const model = ref<string | string[] | null>(null);

const df = new DateFormatter('en-US', {
    dateStyle: 'medium',
});

// Convert string date to CalendarDate object or DateRange for Between clause
const dateValue = computed(() => {
    if (!model.value) return undefined;
    
    // Handle Between clause (date range)
    if (props.filter.selectedClause?.value === 'between' && Array.isArray(model.value)) {
        try {
            if (model.value.length === 2 && model.value[0] && model.value[1]) {
                return {
                    start: parseDate(model.value[0]),
                    end: parseDate(model.value[1])
                };
            }
        } catch {
            return undefined;
        }
    }
    
    // Handle single date clauses
    if (typeof model.value === 'string') {
        try {
            return parseDate(model.value);
        } catch {
            return undefined;
        }
    }
    
    return undefined;
});

const setModelValue = (value: string | string[] | null) => {
    if (!props.inHeader || isDefaultClause.value) {
        model.value = value;
    } else {
        model.value = null;
    }
};

setModelValue(props.filter.value);

const search = (value: string | string[] | null) => {
    let searchValue = '';
    
    if (Array.isArray(value) && value.length === 2) {
        // For Between clause, join dates with comma
        searchValue = value.join(',');
    } else if (typeof value === 'string') {
        searchValue = value;
    }
    
    emit(
        'update',
        searchValue,
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

const onRangeSelect = (range: DateRange | undefined) => {
    if (range && range.start && range.end) {
        // Convert DateRange to array of ISO strings
        const rangeArray = [range.start.toString(), range.end.toString()];
        model.value = rangeArray;
        search(rangeArray);
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

const getDisplayText = () => {
    if (!dateValue.value) return 'Pick a date';
    
    if (props.filter.selectedClause?.value === 'between' && typeof dateValue.value === 'object' && 'start' in dateValue.value) {
        // Display date range
        const start = df.format(toDate(dateValue.value.start));
        const end = df.format(toDate(dateValue.value.end));
        return `${start} - ${end}`;
    } else if (typeof dateValue.value === 'object' && 'day' in dateValue.value) {
        // Display single date
        return df.format(toDate(dateValue.value));
    }
    
    return 'Pick a date';
};

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
        <template v-if="filter.selectedClause?.value !== 'between'">
            <Calendar
                :model-value="dateValue"
                calendar-label="Select date"
                :min-value="new CalendarDate(1900, 1, 1)"
                :max-value="today(getLocalTimeZone())"
                @update:model-value="onDateSelect"
            />
        </template>
        <template v-else>
            <RangeCalendar 
                :model-value="dateValue"
                @update:model-value="onRangeSelect"
                class="rounded-md border" 
            />
        </template>
    </div>

    <!-- Show button with popover calendar in table header (when inHeader) -->
    <Popover v-else>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('h-8 w-full px-2 font-normal', !dateValue && 'text-muted-foreground')">
                <span>{{ getDisplayText() }}</span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <template v-if="filter.selectedClause?.value !== 'between'">
                <Calendar
                    :model-value="dateValue"
                    calendar-label="Select date"
                    :min-value="new CalendarDate(1900, 1, 1)"
                    :max-value="today(getLocalTimeZone())"
                    @update:model-value="onDateSelect"
                />
            </template>
            <template v-else>
                <RangeCalendar 
                    :model-value="dateValue"
                    @update:model-value="onRangeSelect"
                    class="rounded-md border" 
                />
            </template>
        </PopoverContent>
    </Popover>
</template>
