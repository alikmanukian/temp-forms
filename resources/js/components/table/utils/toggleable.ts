import type { TableHeader } from '../index';
import { useComponents } from './components';

export const useToggleColumns = (pageName: string) => {
    const { getColumns, update } = useComponents(pageName);

    const toggleColumn = (columnName: string) => {
        const columns = getColumns();

        columns.forEach((column: TableHeader) => {
            if (column.name === columnName) {
                if (column.visible === undefined) {
                    column.visible = false
                } else {
                    column.visible = !column.visible
                }
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

