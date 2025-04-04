import { type TableHeader } from '@/components/table';
import { computed, nextTick, ref } from 'vue';
import { useComponents } from './components';

export const useStickableColumns = (pageName: string) => {
    const { getColumns, updateColumns } = useComponents(pageName);

    const columns = getColumns();
    // const localStorage = useLocalStorage<TableHeader[]>(getLocalStorageKey(pageName), columns)

    const container = ref<HTMLElement | null>();
    const columnsPositions = ref<Record<string, { left: number; width: number }>>({});

    const stickableColumns = computed(() =>
        columns.filter((column: TableHeader) => column.options.stickable).map((column: TableHeader) => column.name),
    );

    const stickedColumns = computed(() =>
        columns.filter((column: TableHeader) => column.options.sticked).map((column: TableHeader) => column.name),
    );

    const stickColumn = (columnName: string) => {
        updateColumns(
            pageName,
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

    const saveColumnsPositions = () => {
        const table = container.value?.tagName == 'TABLE' ? container.value : container.value?.querySelector('table');
        if (!table) return;

        const tableLeft = table.getBoundingClientRect().left + (table.parentElement?.scrollLeft ?? 0);

        table.querySelectorAll('thead th')?.forEach((th: Element) => {
            const thElement = th as HTMLElement; // ✅ Type assertion
            const name = thElement.dataset.name as string;
            columnsPositions.value[name] = {
                left: th.getBoundingClientRect().left - tableLeft,
                width: th.getBoundingClientRect().width,
            };
        });

        /*localStorage.value.forEach((column: TableHeader) => {
            if (columnsPositions.value[column.name] !== undefined) {
                column.left = columnsPositions.value[column.name].left;
                column.width = columnsPositions.value[column.name].width + 'px';
            }
        });*/

        // tableWidth.value = calculateTableWidth();
    };

    const calculateTableWidth = (): number => {
        let total = 0;

        container.value?.querySelectorAll('table thead th')?.forEach((th: Element) => {
            total = total + th.getBoundingClientRect().width;
        });

        return total;
    };

    const setContainer = (element: HTMLElement | null) => {
        container.value = element;
        // containerWidth.value = container.value?.getBoundingClientRect().width ?? 0;
        // tableWidth.value = calculateTableWidth();
    };

    return {
        columnsPositions,
        stickColumn,
        saveColumnsPositions,
        setContainer,
    };
};
