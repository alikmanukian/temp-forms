import type { TableHeader } from '@/components/table';
import { useComponents } from './components';

export const useToggleColumns = (pageName: string) => {
    const { getColumns, updateColumns } = useComponents(pageName);

    const toggleColumn = (columnName: string) => {
        const columns = getColumns();

        columns.forEach((column: TableHeader) => {
            if (column.name === columnName) {
                column.options.visible = !column.options.visible
            }
        })
    }

    const saveColumnsOrdering = (updatedColumns: TableHeader[]) => {
        updateColumns(updatedColumns)
    }

    return {
        toggleColumn,
        saveColumnsOrdering
    };
}

