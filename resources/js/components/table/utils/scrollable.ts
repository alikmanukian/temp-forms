import { useComponents } from './components';
import { computed, ref } from 'vue';
import { TableHeader } from '@/components/table';

const columnsPositions = ref<Record<string, { left: number; width: number }>>({});
const scrollSize = ref(0);
const showScrollButton = ref(true);
const scrollPosition = ref(0);
const containerWidth = ref(0);

export const useScrollable = (pageName: string) => {
    const { getScrollContainer, getContainerWidth, getTable, updateColumns, getColumns, getContainer } = useComponents(pageName);

    const updateScrollSize = () => {
        const scrollContainer = getScrollContainer();
        if (scrollContainer) {
            scrollSize.value = scrollContainer.scrollWidth || 0;
            console.log('scrollSize', scrollSize.value);
        }
    }

    const updateContainerWidth = () => {
        getContainerWidth().value = getContainer()?.clientWidth || 0
        console.log('containerWidth', getContainerWidth().value);
    }

    /*const updateTableWidth = (): void => {
        let total = 0;

        getTable()?.querySelectorAll('thead th')?.forEach((th: Element) => {
            total = total + th.getBoundingClientRect().width;
        });

        console.log('tableWidth', total);

        tableWidth.value = total;
    };*/

    const scrollToRight = () => {
        const scrollContainer = getScrollContainer();

        if (scrollContainer && scrollSize.value) {
            scrollContainer.scrollTo({ left: scrollSize.value, behavior: 'smooth' });
        }
    };

    const updateScrollPosition = (e: Event) => {
        const target = e.target as HTMLElement;
        scrollPosition.value = target.scrollLeft;
        showScrollButton.value = Math.ceil(target.scrollLeft) < Math.floor(scrollSize.value - getContainerWidth().value);
    };

    const saveColumnsPositions = () => {
        const table = getTable();
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

        const columns = getColumns();

        columns.forEach((column: TableHeader) => {
            if (columnsPositions.value[column.name] !== undefined) {
                column.left = columnsPositions.value[column.name].left;
                column.width = columnsPositions.value[column.name].width + 'px';
            }
        });

        updateColumns(columns); // save columns positions to localstorage
        updateScrollSize();
    };

    const scrollable = computed(() => {
        return scrollSize.value > getContainerWidth().value
    });

    return {
        scrollPosition,
        showScrollButton,
        scrollable,
        columnsPositions,
        scrollToRight,
        updateScrollSize,
        updateScrollPosition,
        updateContainerWidth,
        saveColumnsPositions,
    }
}
