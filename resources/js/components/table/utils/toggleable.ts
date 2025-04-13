import type { TableHeader } from '../index';
import { useComponents } from './components';

export const useToggleColumns = (name: string) => {
    const { getColumns, update } = useComponents(name);

    const toggleColumn = (columnName: string) => {
        const columns = getColumns();
        let resetColumnsWidths = false;

        columns.forEach((column: TableHeader) => {
            if (column.name === columnName) {
                if (column.visible === undefined) {
                    column.visible = false
                } else {
                    column.visible = !column.visible
                }

                if (column.width === undefined || column.width === 'auto' || column.width === '0px') {
                    resetColumnsWidths = true
                }
            }
        })

        if (resetColumnsWidths) {
            columns.forEach((column: TableHeader) => column.width = 'auto')
        }

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

