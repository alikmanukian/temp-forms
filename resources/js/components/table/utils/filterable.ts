
// Get initial search from query string
import { usePage } from '@inertiajs/vue3';
import { buildData } from '../utils/helpers';

export const useFilters = (pageName: string) => {
    const page = usePage<{
        query: any;
    }>();

    const query = page.props.query;

    const getFilterParam = (token: string) => {
        let filterParam = 'filter';

        if (pageName.includes('.')) {
            filterParam += '.' + pageName.split('.')[1];
        }

        filterParam += '.' + token;

        return filterParam;
    }

    const getInitialSearch = (token: string) => {
        return getFilterParam(token).split('.').reduce((acc, key) => {
            if (acc && acc.hasOwnProperty(key)) {
                return acc[key];
            }
            return null;
        }, query)
    }

    const setSearchParams = (token: string, value: any) => {
        return {
            ...buildData(getFilterParam(token), value),
            ...buildData(pageName, 1)
        }
    }

    return {
        query,
        getInitialSearch,
        getFilterParam,
        setSearchParams,
    }
}
