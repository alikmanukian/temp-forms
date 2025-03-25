import  { type TableHeader } from '@/components/table';
import { computed, ref, nextTick } from 'vue';
import { useLocalStorage } from '@vueuse/core';

export const useStickableColumns = (initialColumns: TableHeader[]) => {
    const columns = ref<TableHeader[]>(initialColumns)
    const localStorage = useLocalStorage<TableHeader[]>('table_columns:' + window.location.pathname, columns)

    const container = ref<HTMLElement|null>()
    const columnsPositions = ref<Record<string, {left: number, width: number}>>({});
    const scrollPosition = ref(0);
    const containerWidth = ref(0)
    const tableWidth = ref(0)

    const tableNeedsScroll = computed(() => tableWidth.value > containerWidth.value)

    const stickableColumns = computed(() => columns.value
        .filter((column: TableHeader) => column.options.stickable)
        .map((column: TableHeader) => column.name)
    )

    const stickedColumns = computed(() => columns.value
        .filter((column: TableHeader) => column.options.sticked)
        .map((column: TableHeader) => column.name)
    )

    const lastStickableColumn = computed(() => {
        return columns.value.filter((column) => column.options.stickable).pop();
    });

    const stickColumn = (columnName: string) => {
        localStorage.value = columns.value.map((column: TableHeader) => {
            if (column.name === columnName) {
                // unstick
                if (column.options.sticked) {
                    // unstick also all other columns after the column
                    const index = stickedColumns.value.indexOf(columnName);

                    column.options.sticked = false;
                    columns.value.forEach((column: TableHeader) => {
                        if (stickableColumns.value.indexOf(column.name) > index) {
                            column.options.sticked = false;
                        }
                    })
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
        })

        // save column positions
        nextTick(() => saveColumnsPositions()).then(() => {})
    }

    const saveColumnsPositions = () => {
        const table = container.value?.tagName == 'TABLE' ? container.value : container.value?.querySelector("table");
        if (!table) return;

        const tableLeft = table.getBoundingClientRect().left + (table.parentElement?.scrollLeft ?? 0);

        table.querySelectorAll('thead th')
            ?.forEach((th: Element) => {
                const thElement = th as HTMLElement; // ✅ Type assertion
                const name = thElement.dataset.name as string
                columnsPositions.value[name] = {
                    left: th.getBoundingClientRect().left - tableLeft,
                    width: th.getBoundingClientRect().width,
                };
            });

        localStorage.value.forEach((column: TableHeader) => {
            if (columnsPositions.value[column.name] !== undefined) {
                column.left = columnsPositions.value[column.name].left;
                column.width = columnsPositions.value[column.name].width + 'px';
            }
        })

        tableWidth.value = calculateTableWidth();
    }

    const updateScrollPosition = (e: Event) => {
        const target = e.target as HTMLElement;

        if (target.scrollLeft === 0) {
            scrollPosition.value = 0;
        }

        if (target?.scrollLeft > 0 && scrollPosition.value === 0) {
            scrollPosition.value = target.scrollLeft;
        }
    }

    const calculateTableWidth = (): number => {
        let total = 0

        container.value?.querySelectorAll('table thead th')
            ?.forEach((th: Element) => {
                total = total + th.getBoundingClientRect().width
            })

        return total
    }

    const setContainer = (element: HTMLElement|null) => {
        container.value = element
        containerWidth.value = container.value?.getBoundingClientRect().width ?? 0
        tableWidth.value = calculateTableWidth()
    }

    return {
        lastStickableColumn,
        columnsPositions,
        scrollPosition,
        tableNeedsScroll,
        stickColumn,
        updateScrollPosition,
        saveColumnsPositions,
        setContainer,
    };
}
