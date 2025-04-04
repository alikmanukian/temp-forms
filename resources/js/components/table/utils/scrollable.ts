import { useComponents } from './components';
import { computed, ref } from 'vue';



export const useScrollable = (pageName: string) => {
    const { getScrollContainer } = useComponents();

    const showScrollButton = ref(true);

    const scrollSize = ref(0);
    const scrollPosition = ref(0);
    const containerWidth = ref(0);
    const tableWidth = ref(0);

    const calculateScrollSize = () => {
        const scrollContainer = getScrollContainer(pageName);
        if (scrollContainer) {
            scrollSize.value = scrollContainer.scrollWidth || 0;
            console.log('scrollSize', scrollSize.value);
        }
    }

    const scrollToRight = () => {
        const el = getScrollContainer(pageName);

        if (el && scrollSize.value) {
            el.scrollTo({ left: scrollSize.value, behavior: 'smooth' });
        }
    };

    const updateScrollPosition = (e: Event) => {
        const target = e.target as HTMLElement;
        scrollPosition.value = target.scrollLeft;
        showScrollButton.value = Math.ceil(target.scrollLeft) < Math.floor(tableWidth.value - containerWidth.value);
    };

    const scrollable = computed(() => tableWidth.value > containerWidth.value);

    return {
        scrollPosition,
        showScrollButton,
        scrollable,
        scrollToRight,
        calculateScrollSize,
        updateScrollPosition
    }
}
