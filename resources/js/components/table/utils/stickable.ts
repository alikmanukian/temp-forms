import { type TableHeader } from '../index';
import { computed, nextTick } from 'vue';
import { useComponents } from './components';
import { useScrollable } from '../utils/scrollable';

export const useStickableColumns = (name: string) => {
    const { getColumns, update } = useComponents(name);
    const { saveColumnsPositions } = useScrollable(name);

    const columns = getColumns();

    const stickableColumns = computed(() =>
        columns.filter((column: TableHeader) => column.options.stickable).map((column: TableHeader) => column.name),
    );

    const stickedColumns = computed(() =>
        columns.filter((column: TableHeader) => column.sticked).map((column: TableHeader) => column.name),
    );

    const stickColumn = (columnName: string) => {
        update(
            'columns',
            columns.map((column: TableHeader) => {
                if (column.name === columnName) {
                    // unstick
                    if (column.sticked) {
                        // unstick also all other columns after the column
                        const index = stickedColumns.value.indexOf(columnName);

                        column.sticked = false;
                        columns.forEach((column: TableHeader) => {
                            if (stickableColumns.value.indexOf(column.name) > index) {
                                column.sticked = false;
                            }
                        });
                    }
                    // stick
                    else {
                        // stick only the column is last stickable column
                        if (stickedColumns.value.length === stickableColumns.value.indexOf(column.name)) {
                            column.sticked = true;
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
