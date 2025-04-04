import { nextTick } from 'vue';

const init = (el: HTMLElement) => {
    const ths = el.querySelectorAll("th");

    ths.forEach((th) => {
        const div = th.querySelector('[data-name="header-button-container"]');

        let minLeft = 0;
        let dragging = false;

        const resizer = document.createElement("div");
        resizer.style.position = "absolute";
        resizer.style.top = "0";
        resizer.style.right = "0";
        resizer.style.width = "5px";
        resizer.style.height = "100%";
        resizer.style.cursor = "col-resize";
        resizer.style.userSelect = "none";
        resizer.style.backgroundColor = "transparent";
        resizer.style.zIndex = "10";

        resizer.addEventListener("mouseenter", () => {
            resizer.style.backgroundColor = "#ddd"; // Highlight on hover
        });

        resizer.addEventListener("mouseleave", (e) => {
            if (! dragging) {
                resizer.style.backgroundColor = "transparent";
            }
        });

        resizer.addEventListener("mousedown", (e) => {
            e.preventDefault();
            const startX = e.clientX;
            const startWidth = th.offsetWidth;
            dragging = true;

            minLeft = th.querySelector('[data-name="header-button"]')?.getBoundingClientRect().width ?? 0;

            document.onmousemove = (e) => {
                const newWidth = Math.max(startWidth + (e.clientX - startX), minLeft);
                th.style.width = `${newWidth}px`;
            };

            document.onmouseup = () => {
                document.onmousemove = null;
                document.onmouseup = null;
                resizer.style.backgroundColor = "transparent";
                dragging = false;

                el.dispatchEvent(new CustomEvent("resize:end"));
            };

            el.dispatchEvent(new CustomEvent("resize:start"));
        });

        div?.appendChild(resizer);
    });
}
const vResizable = {
    mounted(el: HTMLElement, binding: {value: boolean}) {
        // Skip if condition is provided and evaluates to false
        if (! binding.value) return;

        nextTick(() => init(el))
    },
};

export default vResizable;
