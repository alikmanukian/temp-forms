import type { TableHeader as TableHeaderType, TableHeader } from '@/components/table';
import { computed, onMounted, ref, useTemplateRef, nextTick, ShallowRef } from 'vue';
import { useLocalStorage } from '@vueuse/core';

export const useStickableColumns = (initialColumns: TableHeader[]) => {
    const columns = ref<TableHeader[]>(initialColumns)
    const localStorage = useLocalStorage<TableHeader[]>('table_columns:' + window.location.pathname, columns)
    const columnsPositions = ref<Record<string, {left: number, width: number}>>({});
    const scrollPosition = ref(0);

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

    const stickColumn = (columnName: string, container: HTMLElement|null) => {
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
        nextTick(() => saveColumnsPositions(container))
    }

    const saveColumnsPositions = (container: HTMLElement|null) => {
        const table = container?.tagName == 'TABLE' ? container : container?.querySelector("table");
        if (!table) return;

        const tableLeft = table.getBoundingClientRect().left;

        table.querySelectorAll('thead th')
            .forEach((th) => {
                const thElement = th as HTMLElement; // ✅ Type assertion
                const name = thElement.dataset.name as string
                columnsPositions.value[name] = {
                    left: th.getBoundingClientRect().left - tableLeft,
                    width: th.getBoundingClientRect().width,
                };
            });

        localStorage.value.forEach((column: TableHeader) => {
            column.left = columnsPositions.value[column.name].left;
            column.width = columnsPositions.value[column.name].width + 'px';
        })
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

    return {
        lastStickableColumn,
        columnsPositions,
        scrollPosition,
        stickColumn,
        updateScrollPosition,
        saveColumnsPositions
    };
}
