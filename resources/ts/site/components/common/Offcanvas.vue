<template>
    <div
        class="app-offcanvas"
        :class="{ 'is-open': offcanvasVisibleState[props.name] }"
        @click="clickHandler"
    >
        <div class="app-offcanvas__body">
            <slot></slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    offcanvasToggle,
    offcanvasVisibleState,
} from "@site/composables/useOffcanvas";

const props = defineProps<{
    name: string;
}>();

offcanvasVisibleState.value[props.name] = false;

function clickHandler(event: Event) {
    const target = event.target as HTMLElement;
    if (target.classList.contains("app-offcanvas")) {
        offcanvasToggle(props.name);
    }
}
</script>

<style lang="scss">
.app-offcanvas {
    @apply fixed
        left-0
        top-0
        bottom-0
        right-0
        z-[100]
        transition-all
        duration-300
        pointer-events-none
        opacity-0
        backdrop-blur-sm
        bg-dark
        bg-opacity-50;

    &__body {
        @apply transition-all
            duration-300
            -translate-x-[120%]
            bg-light
            w-[280px]
            h-full
            overflow-y-auto
            p-5
            pb-10
            shadow-xl;
    }

    &.is-open {
        @apply opacity-100
            pointer-events-auto;

        .app-offcanvas__body {
            @apply translate-x-[0];
        }
    }
}
</style>
