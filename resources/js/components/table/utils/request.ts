import { ref } from 'vue';
import type { VisitOptions } from '@inertiajs/core';
import { router } from '@inertiajs/vue3';
import { type SearchParams, processSearchData } from './filterable';

export const loading = ref<boolean>(false);

export const useRequest = (name: string) => {
    const reload = (searchData: SearchParams, options: VisitOptions = {}) => {

        let params: VisitOptions = {
            only: ['query', name],
            onStart: () => (loading.value = true),
            onFinish: () => (loading.value = false),
            replace: true, // todo remove
            preserveState: true,
            preserveScroll: true,
        };

        params = {...params, ...options};

        // Get current query parameters
        const urlSearchParams = new URLSearchParams(window.location.search);
        const currentParams: Record<string, any> = {};

        // First, parse all current URL parameters
        for (const [key, value] of urlSearchParams.entries()) {
            // Handle nested parameters like filter[name], filter[users][email]
            if (key.includes('[') && key.includes(']')) {
                const parts = key.replace(/\]/g, '').split('[');
                let current = currentParams;

                for (let i = 0; i < parts.length - 1; i++) {
                    if (!current[parts[i]]) {
                        current[parts[i]] = {};
                    }
                    current = current[parts[i]];
                }

                current[parts[parts.length - 1]] = value;
            } else {
                currentParams[key] = value;
            }
        }

        // Process the new search data to remove only explicitly empty values
        const processedData = processSearchData(searchData, currentParams);

        // Remove page if it's 1 (default page)
        if (processedData.page === 1) {
            delete processedData.page;
        }

        // Handle nested page parameters
        if (processedData.page && typeof processedData.page === 'object') {
            Object.entries(processedData.page).forEach(([key, value]) => {
                if (value === 1) {
                    delete (processedData.page as Record<string, number>)[key];
                }
            });

            // Remove the entire page object if it's empty after processing
            if (Object.keys(processedData.page).length === 0) {
                delete processedData.page;
            }
        }

        params.data = processedData;

        // Completely replace parameters instead of merging
        router.visit(window.location.pathname, params);
    };

    return {
        loading,
        reload,
    }
}
