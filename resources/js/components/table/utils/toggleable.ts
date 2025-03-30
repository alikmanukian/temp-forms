import type { TableHeader } from '@/components/table';
import { getColumns, updateColumns } from './components';

export const useToggleColumns = (pageName: string) => {
    const toggleColumn = (columnName: string) => {
        const columns = getColumns(pageName) as TableHeader[];

        columns.forEach((column: TableHeader) => {
            if (column.name === columnName) {
                column.options.visible = !column.options.visible
            }
        })
    }

    const saveColumnsOrdering = (updatedColumns: TableHeader[]) => {
        updateColumns(pageName, updatedColumns)
    }

    return {
        toggleColumn,
        saveColumnsOrdering
    };
}

