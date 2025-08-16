import type { TableHeader as TableHeaderType } from '@/components/table';

/**
 * Example usage:
 * const key1 = 'page';
 * const key2 = 'page.something';
 *
 * console.log(buildData(key1, 'test')); // → { page: 'test' }
 * console.log(buildData(key2, 'test')); // → { page: { something: 'test' } }
 */
export const buildData = (key: string, value: any) => {
    return key
        .split('.')
        .reverse()
        .reduce((acc, part) => ({ [part]: acc }), value);
};

/**
 * Example usage:
 *
 * console.log(buildQueryKey('page'));                 // → "page"
 * console.log(buildQueryKey('page.something'));       // → "page[something]"
 * console.log(buildQueryKey('page.something.else'));  // → "page[something][else]"
 */
export const buildQueryKey = (input: string): string => {
    if (input.includes('.')) {
        const [parent, child] = input.split('.');
        return `${parent}[${child}]`;
    }
    return input;
};

export const getQueryParams = (except?: string): Record<string, string> => {
    const queryParams = new URLSearchParams(window.location.search);

    if (except) {
        return Object.fromEntries([...queryParams].filter(([key]) => key !== except));
    }

    return Object.fromEntries([...queryParams]);
};

export const columnWrappingMethod = (column: TableHeaderType, stickyHeader: boolean): string => {
    if (column.options.truncate === 1) {
        return 'truncate';
    }

    if (column.options.truncate > 1) {
        return `line-clamp-${column.options.truncate}`;
    }

    if (column.options.wrap) {
        return '';
    }

    if (!stickyHeader) {
        return 'whitespace-nowrap';
    }

    return '';
};
