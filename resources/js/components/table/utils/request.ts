import { ref } from 'vue';
import type { VisitOptions } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';

// Interfaces for 2-level nested filter structure
interface NestedFilter {
    [key: string]: string | number | null;
}

interface FilterWithNesting {
    [key: string]: NestedFilter;
}

// Interface for 1-level filter structure
interface FlatFilter {
    [key: string]: string | number | null;
}

// Union type for filter (can be either nested or flat)
type Filter = FilterWithNesting | FlatFilter;

// Interface for the full object
interface DataObject {
    filter: Filter;
    [key: string]: any;
}

const cleanFilter = (obj: DataObject): DataObject => {
    // Create a copy to avoid modifying the original object
    const cleanedObj: DataObject = { ...obj, filter: {} };

    // Iterate through filter properties
    for (const key in obj.filter) {
        const value = obj.filter[key];

        // Handle 2-level nesting (e.g., users: { name: "" })
        if (typeof value === 'object' && !Array.isArray(value)) {
            const nestedClean: NestedFilter = {};
            let hasNonEmpty = false;

            // Check nested properties
            for (const nestedKey in value) {
                if (value[nestedKey] !== '' && value[nestedKey] != null) {
                    nestedClean[nestedKey] = value[nestedKey];
                    hasNonEmpty = true;
                }
            }

            // Only add to filter if there are non-empty values
            if (hasNonEmpty) {
                cleanedObj.filter[key] = nestedClean;
            }
        }
        // Handle 1-level (e.g., name: "")
        else if (value !== '' && value != null) {
            cleanedObj.filter[key] = value;
        }
    }

    // Remove filter entirely if it's empty
    if (Object.keys(cleanedObj.filter).length === 0) {
        delete cleanedObj.filter;
        // remove from url also
    }

    console.log(cleanedObj);

    return cleanedObj;
}

export const loading = ref<boolean>(false);

export const useRequest = (name: string) => {
    const reload = (data: any) => {
        const params: VisitOptions = {
            only: ['query', name],
            onStart: () => (loading.value = true),
            onFinish: () => (loading.value = false),
        };

        // todo: clear empty filters, clear page=1
        params.data = cleanFilter(data);

        router.reload(params);
    };

    return {
        loading,
        reload,
    }
}
