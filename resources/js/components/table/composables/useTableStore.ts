import { useTableStore } from '@/stores/table';
import type { Filter, Clause } from '@/components/table';

export const useTableOperations = (tableName: string) => {
    const tableStore = useTableStore();
    
    // Computed properties
    const filters = tableStore.getFilters(tableName);
    const searchString = tableStore.getSearchString(tableName);
    const loading = tableStore.getLoading(tableName);
    
    const getFiltersAreApplied = (initialFilters: Filter[]) => 
        tableStore.filtersAreApplied(tableName);
        
    const getShowFiltersRowInHeader = (initialFilters: Filter[]) => 
        tableStore.showFiltersRowInHeader(tableName, initialFilters);
    
    // Actions
    const searchBy = (field: string, value: string | string[], clause: string | null, callback?: (filter?: Filter) => void) => {
        tableStore.searchBy(tableName, field, value, clause, callback);
    };
    
    const onUpdateFilter = (field: string, value: string | string[], clause: Clause | null) => {
        tableStore.updateFilter(tableName, field, value, clause);
    };
    
    const onDeleteFilter = (field: string) => {
        tableStore.deleteFilter(tableName, field);
    };
    
    const onAddFilter = (name: string) => {
        tableStore.addFilter(tableName, name);
    };
    
    const resetFilters = () => {
        tableStore.resetFilters(tableName);
    };
    
    const updateSearchString = (value: string) => {
        tableStore.updateSearchString(tableName, value);
    };
    
    const onPageChange = (page: number) => {
        tableStore.changePage(tableName, page);
    };
    
    const reload = (searchData: any, options: any = {}) => {
        tableStore.reload(tableName, searchData, options);
    };
    
    return {
        // Computed
        filters,
        searchString,
        loading,
        getFiltersAreApplied,
        getShowFiltersRowInHeader,
        
        // Actions
        searchBy,
        onUpdateFilter,
        onDeleteFilter,
        onAddFilter,
        resetFilters,
        updateSearchString,
        onPageChange,
        reload,
    };
};