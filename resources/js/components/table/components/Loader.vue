<template>
    <div>
        <div class="fade-in-delayed">
            <svg
                version="1.1"
                id="L5"
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                x="0px"
                y="0px"
                viewBox="0 0 100 100"
                enable-background="new 0 0 0 0"
                xml:space="preserve"
                class="mt-24 flex size-24 items-center justify-center text-black/50"
            >
                <circle fill="currentColor" stroke="none" cx="24" cy="50" r="6">
                    <animateTransform
                        attributeName="transform"
                        dur="1s"
                        type="translate"
                        values="0 15 ; 0 -15; 0 15"
                        repeatCount="indefinite"
                        begin="0.1"
                    />
                </circle>
                <circle fill="currentColor" stroke="none" cx="48" cy="50" r="6">
                    <animateTransform
                        attributeName="transform"
                        dur="1s"
                        type="translate"
                        values="0 10 ; 0 -10; 0 10"
                        repeatCount="indefinite"
                        begin="0.2"
                    />
                </circle>
                <circle fill="currentColor" stroke="none" cx="72" cy="50" r="6">
                    <animateTransform
                        attributeName="transform"
                        dur="1s"
                        type="translate"
                        values="0 5 ; 0 -5; 0 5"
                        repeatCount="indefinite"
                        begin="0.3"
                    />
                </circle>
            </svg>
        </div>
    </div>
</template>

<style scoped>
.fade-in-delayed {
    opacity: 0;
    animation: fadeIn 0.3s ease-in-out 0.3s forwards;
}

@keyframes fadeIn {
    to {
        opacity: 1;
    }
}
</style>
