import { type TableHeader } from '@/components/table';
import { computed, nextTick } from 'vue';
import { useComponents } from './components';
import { useScrollable } from '@/components/table/utils/scrollable';

export const useStickableColumns = (pageName: string) => {
    const { getColumns, updateColumns } = useComponents(pageName);
    const { saveColumnsPositions } = useScrollable(pageName);

    const columns = getColumns();
    // const localStorage = useLocalStorage<TableHeader[]>(getLocalStorageKey(pageName), columns)

    const stickableColumns = computed(() =>
        columns.filter((column: TableHeader) => column.options.stickable).map((column: TableHeader) => column.name),
    );

    const stickedColumns = computed(() =>
        columns.filter((column: TableHeader) => column.options.sticked).map((column: TableHeader) => column.name),
    );

    const stickColumn = (columnName: string) => {
        updateColumns(
            columns.map((column: TableHeader) => {
                if (column.name === columnName) {
                    // unstick
                    if (column.options.sticked) {
                        // unstick also all other columns after the column
                        const index = stickedColumns.value.indexOf(columnName);

                        column.options.sticked = false;
                        columns.forEach((column: TableHeader) => {
                            if (stickableColumns.value.indexOf(column.name) > index) {
                                column.options.sticked = false;
                            }
                        });
                    }
                    // stick
                    else {
                        // stick only the column is last stickable column
                        if (stickedColumns.value.length === stickableColumns.value.indexOf(column.name)) {
                            column.options.sticked = true;
                        }
                    }
                }

                return column;
            }),
        );

        // save column positions
        nextTick(() => saveColumnsPositions()).then(() => {});
    };

    return {
        stickColumn,
    };
};
