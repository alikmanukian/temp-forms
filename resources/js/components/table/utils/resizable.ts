const vResizable = {
    mounted(el: HTMLElement, binding: {value: boolean}) {
        // Skip if condition is provided and evaluates to false
        if (! binding.value) return;

        const ths = el.querySelectorAll("th");

        ths.forEach((th) => {
            th.style.position = "relative"; // Ensure relative positioning for the resizer

            const resizer = document.createElement("div");
            resizer.style.position = "absolute";
            resizer.style.top = "0";
            resizer.style.right = "0";
            resizer.style.width = "5px";
            resizer.style.height = "100%";
            resizer.style.cursor = "col-resize";
            resizer.style.userSelect = "none";
            resizer.style.backgroundColor = "transparent";

            resizer.addEventListener("mouseenter", () => {
                resizer.style.backgroundColor = "#ddd"; // Highlight on hover
            });

            resizer.addEventListener("mouseleave", () => {
                resizer.style.backgroundColor = "transparent";
            });

            resizer.addEventListener("mousedown", (e) => {
                e.preventDefault();
                const startX = e.clientX;
                const startWidth = th.offsetWidth;

                document.onmousemove = (e) => {
                    const newWidth = startWidth + (e.clientX - startX);
                    th.style.width = `${newWidth}px`;
                };

                document.onmouseup = () => {
                    document.onmousemove = null;
                    document.onmouseup = null;

                    el.dispatchEvent(new CustomEvent("resize:end"));
                };

                el.dispatchEvent(new CustomEvent("resize:start"));
            });

            th.appendChild(resizer);
        });
    },
};

export default vResizable;
