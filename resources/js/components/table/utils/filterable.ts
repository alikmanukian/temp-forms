// Get initial search from query string
import { usePage } from '@inertiajs/vue3';
import { buildData } from '../utils/helpers';
import type { Clause, Filter, Paginated } from '@/components/table';
import { useRequest } from '@/components/table/utils/request';
import { reactive, Ref } from 'vue';

interface FilterValue {
    [key: string]: string | number | FilterValue | null;
}

export interface SearchParams {
    filter?: {
        [key: string]: string | FilterValue | null;
    };
    page?:
        | number
        | {
              [key: string]: number;
          };

    [key: string]: any;
}

const possibleEmptyValues = ['', null, '*', '!*', '^', '!^', '$', '!$', '!', '~', '!~'];

const deepMerge = (target: any, source: any) => {
    for (const key in source) {
        if (
            source[key] instanceof Object &&
            key in target &&
            target[key] instanceof Object
        ) {
            deepMerge(target[key], source[key])
        } else {
            target[key] = structuredClone(source[key]) // or use JSON.parse(JSON.stringify(...)) for older browsers
        }
    }
    return target
}

/**
 * Process search data, merging with current params and removing explicitly empty values
 */
export const processSearchData = (newData: SearchParams, currentParams: Record<string, any>): Record<string, any> => {
    const result = { ...currentParams };

    // Process each key in the new data
    Object.entries(newData).forEach(([key, value]) => {
        if (key === 'filter' && typeof value === 'object' && value !== null) {
            // Initialize filter object if it doesn't exist
            if (!result.filter) {
                result.filter = {};
            }

            // Process each filter entry
            processFilterValues(value, result.filter);
        } else if (key === 'page') {
            // For page, simply replace the current value
            result.page = value;
        } else {
            // For other parameters, replace with new value
            result[key] = value;
        }
    });

    return result;
};

/**
 * Recursively process filter values, removing those explicitly set to empty or null
 */
const processFilterValues = (newValues: Record<string, any>, currentValues: Record<string, any>): void => {
    Object.entries(newValues).forEach(([key, value]) => {
        // For nested filter objects
        if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
            if (!currentValues[key] || typeof currentValues[key] !== 'object') {
                currentValues[key] = {};
            }
            processFilterValues(value, currentValues[key]);

            // Remove empty objects
            if (Object.keys(currentValues[key]).length === 0) {
                delete currentValues[key];
            }
        }
        // For explicit empty values or null - remove them
        else if (possibleEmptyValues.includes(value)) {
            delete currentValues[key];
        }
        // For other values - update them
        else {
            currentValues[key] = value;
        }
    });
};

export const clauseShouldNotHaveValue = (clause: Clause) => {
    return ['is_set', 'is_not_set'].includes(clause.value);
};

/**
 * Recursively cleans an object, removing empty string values
 */

export const useFilters = (pageName: string, tableName: string, initialFilters: Filter[]|Record<string, Filter>) => {
    const page = usePage<{
        query: any;
    }>();

    const { reload } = useRequest(tableName);

    // Initialize filters
    let filters = reactive<Record<string, Filter>>({})
    if (Array.isArray(initialFilters)) {
        initialFilters.forEach((filter: Filter) => {
            filters[filter.name] = {...filter}
        });
    } else {
        filters = initialFilters;
    }

    const query = page.props.query;

    const getFilterParam = (token: string) => {
        let filterParam = 'filter';

        if (pageName.includes('.')) {
            filterParam += '.' + pageName.split('.')[1];
        }

        filterParam += '.' + token;

        return filterParam;
    };

    const getInitialSearch = (token: string) => {
        return getFilterParam(token)
            .split('.')
            .reduce((acc, key) => {
                if (acc && acc.hasOwnProperty(key)) {
                    return acc[key];
                }
                return null;
            }, query);
    };

    const setSearchParams = (token: string, value: any) => {
        return {
            ...buildData(getFilterParam(token), value),
            ...buildData(pageName, 1),
        };
    };

    const search = (searchParams: object, params: object) => {
        reload(searchParams, {
            preserveState: true,
            preserveScroll: true,
            ...params
        });
    }

    const searchBy = (field: string, value: string|string[], clause: string|null, callback?: (filter?: Filter) => void) => {
        if (Array.isArray(value)) {
            value = value.join(',')
        }

        if (value === null) {
            value = '';
        }

        if (clause) {
            value = clause + value;
        }

        search(setSearchParams(field, value), {
            onSuccess: (response: any) => {
                const filter = (response.props[tableName] as Paginated<any>).filters.find((filter: Filter) => {
                    return filter.name === field;
                });

                if (callback) {
                    callback(filter);
                }

                if (filter) {
                    filters[field] = filter;
                }

            }
        })
    };

    const resetSearch = () => {
        let searchParams: Record<string, any> = {};

        // set empty filters
        Object.keys(filters).forEach(key => {
          searchParams = deepMerge(searchParams, setSearchParams(key, ''));
        });

        // set empty global search string
        searchParams = deepMerge(searchParams, setSearchParams('search', ''));

        search(searchParams, {
            onSuccess: () => {
                // clear filters
                Object.keys(filters).forEach(key => {
                    filters[key].selected = false
                    filters[key].value = null
                });
            }
        });
    }

    const onUpdateFilter = (field: string, value: string|string[], clause: Clause|null) => {
        searchBy(field, value, clause?.searchSymbol ?? null, (filter?: Filter) => {
            if (filter) {
                filter.selected = true;
                filter.selectedClause = clause;
            }
        });
    };

    const onDeleteFilter = (field: string) => {
        searchBy(field, '', null);
    };

    const onAddFilter = (name: string) => {
        filters[name] = {
            ...filters[name],
            selected: true,
            opened: true,
            selectedClause: filters[name].defaultClause,
        };
    }

    return {
        query,
        filters,
        getInitialSearch,
        getFilterParam,
        setSearchParams,
        searchBy,
        onUpdateFilter,
        onDeleteFilter,
        onAddFilter,
        resetSearch,
    };
};
