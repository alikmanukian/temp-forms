import type { TableHeader } from '../index';
import { useComponents } from './components';

export const useToggleColumns = (pageName: string) => {
    const { getColumns, update } = useComponents(pageName);

    const toggleColumn = (columnName: string) => {
        const columns = getColumns();

        columns.forEach((column: TableHeader) => {
            if (column.name === columnName) {
                column.options.visible = !column.options.visible
            }
        })

        update('columns', columns)
    }

    const saveColumnsOrdering = (updatedColumns: TableHeader[]) => {
        update('columns', updatedColumns)
    }

    return {
        toggleColumn,
        saveColumnsOrdering
    };
}

