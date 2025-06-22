import { useComponents } from './components';
import { computed, ref } from 'vue';
import { TableHeader } from '../index';


export const useScrollable = (name: string) => {
    const { getScrollContainer, getProperty, getTable, update, getColumns, getContainer } = useComponents(name);

    const updateScrollSize = () => {
        const scrollContainer = getScrollContainer();
        if (scrollContainer) {
            update('scrollSize', scrollContainer.scrollWidth || 0)
        }
    }

    const updateContainerWidth = () => {
        update('containerWidth', getContainer()?.clientWidth || 0)
    }

    /*const updateTableWidth = (): void => {
        let total = 0;

        getTable()?.querySelectorAll('thead th')?.forEach((th: Element) => {
            total = total + th.getBoundingClientRect().width;
        });

        tableWidth.value = total;
    };*/

    const scrollToRight = () => {
        const scrollContainer = getScrollContainer();
        const scrollSize = getProperty('scrollSize', 0);

        if (scrollContainer && scrollSize) {
            scrollContainer.scrollTo({ left: scrollSize, behavior: 'smooth' });
        }
    };

    const updateScrollPosition = (e: Event) => {
        const target = e.target as HTMLElement;
        update('scrollPosition', target.scrollLeft)
        update('showScrollButton', Math.ceil(target.scrollLeft) < Math.floor(getProperty('scrollSize', 0) - getProperty('containerWidth', 0)))
    };

    const saveColumnsPositions = () => {
        const table = getTable();
        if (!table) return;

        const tableLeft = table.getBoundingClientRect().left + (table.parentElement?.scrollLeft ?? 0);

        const positions = ref<Record<string, { left: number; width: number }>>({});

        table.querySelectorAll('thead th')?.forEach((th: Element) => {
            const thElement = th as HTMLElement; // ✅ Type assertion
            const name = thElement.dataset.name as string;
            positions.value[name] = {
                left: th.getBoundingClientRect().left - tableLeft,
                width: th.getBoundingClientRect().width,
            };
        });

        const columns = getColumns();

        columns.forEach((column: TableHeader) => {
            if (positions.value[column.name] !== undefined) {
                column.left = positions.value[column.name].left;
                column.width = positions.value[column.name].width + 'px';
            }
        });

        update('columns', columns); // save columns positions to localstorage
        updateScrollSize();
    };

    const scrollable = computed(() => {
        return getProperty('scrollSize', 0) > getProperty('containerWidth', 0)
    });

    const showScrollButton = computed(() => getProperty('showScrollButton', false))
    const scrollPosition = computed(() => getProperty('scrollPosition', 0))

    return {
        scrollPosition,
        showScrollButton,
        scrollable,
        scrollToRight,
        updateScrollSize,
        updateScrollPosition,
        updateContainerWidth,
        saveColumnsPositions,
    }
}
