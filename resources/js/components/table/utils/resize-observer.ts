// Simple debounce implementation
function debounce<T extends (...args: any[]) => any>(func: T, delay: number): (...args: Parameters<T>) => void {
    let timeoutId: number | undefined;
    return (...args: Parameters<T>) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay) as unknown as number;
    };
}

interface ResizeCallback {
    id: string;
    callback: () => void;
    delay?: number;
}

class GlobalResizeObserver {
    private observer: ResizeObserver | null = null;
    private callbacks = new Map<Element, ResizeCallback[]>();
    private debouncedCallbacks = new Map<string, () => void>();

    constructor() {
        if (typeof window !== 'undefined') {
            this.observer = new ResizeObserver(this.handleResize.bind(this));
        }
    }

    private handleResize(entries: ResizeObserverEntry[]): void {
        entries.forEach(entry => {
            const callbacks = this.callbacks.get(entry.target);
            if (callbacks) {
                callbacks.forEach(({ id }) => {
                    const debouncedCallback = this.debouncedCallbacks.get(id);
                    if (debouncedCallback) {
                        debouncedCallback();
                    }
                });
            }
        });
    }

    observe(element: Element, callback: () => void, options: { id: string; delay?: number } = { id: 'default' }): void {
        if (!this.observer) return;

        const { id, delay = 16 } = options; // Default ~60fps

        // Create debounced callback if it doesn't exist
        if (!this.debouncedCallbacks.has(id)) {
            this.debouncedCallbacks.set(id, debounce(callback, delay));
        }

        // Add callback to element
        const existingCallbacks = this.callbacks.get(element) || [];
        const callbackData: ResizeCallback = { id, callback, delay };
        
        // Remove existing callback with same id if it exists
        const filteredCallbacks = existingCallbacks.filter(cb => cb.id !== id);
        this.callbacks.set(element, [...filteredCallbacks, callbackData]);

        // Start observing if not already
        this.observer.observe(element);
    }

    unobserve(element: Element, id?: string): void {
        if (!this.observer) return;

        const callbacks = this.callbacks.get(element);
        if (!callbacks) return;

        if (id) {
            // Remove specific callback
            const filteredCallbacks = callbacks.filter(cb => cb.id !== id);
            if (filteredCallbacks.length === 0) {
                this.callbacks.delete(element);
                this.observer.unobserve(element);
            } else {
                this.callbacks.set(element, filteredCallbacks);
            }
            
            // Clean up debounced callback if no other elements use it
            const stillInUse = Array.from(this.callbacks.values())
                .flat()
                .some(cb => cb.id === id);
            
            if (!stillInUse) {
                this.debouncedCallbacks.delete(id);
            }
        } else {
            // Remove all callbacks for this element
            callbacks.forEach(({ id }) => {
                const stillInUse = Array.from(this.callbacks.values())
                    .flat()
                    .filter(cb => cb.id === id).length > 1;
                
                if (!stillInUse) {
                    this.debouncedCallbacks.delete(id);
                }
            });
            
            this.callbacks.delete(element);
            this.observer.unobserve(element);
        }
    }

    disconnect(): void {
        if (this.observer) {
            this.observer.disconnect();
            this.callbacks.clear();
            this.debouncedCallbacks.clear();
        }
    }

    // Get stats for debugging
    getStats() {
        return {
            observedElements: this.callbacks.size,
            totalCallbacks: Array.from(this.callbacks.values()).reduce((acc, callbacks) => acc + callbacks.length, 0),
            debouncedCallbacks: this.debouncedCallbacks.size,
        };
    }
}

// Global singleton instance
const globalResizeObserver = new GlobalResizeObserver();

export { globalResizeObserver };

// Composable for easier usage in Vue components
export const useResizeObserver = () => {
    const observe = (
        element: Element, 
        callback: () => void, 
        options: { id: string; delay?: number } = { id: 'default' }
    ) => {
        globalResizeObserver.observe(element, callback, options);
    };

    const unobserve = (element: Element, id?: string) => {
        globalResizeObserver.unobserve(element, id);
    };

    const disconnect = () => {
        globalResizeObserver.disconnect();
    };

    const getStats = () => {
        return globalResizeObserver.getStats();
    };

    return {
        observe,
        unobserve,
        disconnect,
        getStats,
    };
};