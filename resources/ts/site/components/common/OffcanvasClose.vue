<template>
    <div
        class="offcanvas-close text-brand text-14 flex gap-2 items-center cursor-pointer transition-all duration-300"
        :class="
            offcanvasVisibleAvailable
                ? 'opacity-1 pointer-events-auto translate-x-0'
                : 'opacity-0 pointer-events-none -translate-x-[120%]'
        "
        @click="offcanvasClose()"
    >
        <div class="svg-icon w-4 h-4">
            <svg><use xlink:href="#arrow-left"></use></svg>
        </div>
        <div>назад</div>
    </div>
</template>

<script setup lang="ts">
import {
    offcanvasClose,
    offcanvasVisibleAvailable,
} from "@site/composables/useOffcanvas";
</script>
