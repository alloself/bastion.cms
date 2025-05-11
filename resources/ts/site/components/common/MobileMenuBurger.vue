<template>
    <button
        type="button"
        class="rounded-icon"
        @click="offcanvasToggle('mobileMenu')"
    >
        <div
            class="svg-icon w-8 h-8"
            v-if="offcanvasVisibleState['mobileMenu']"
        >
            <svg><use xlink:href="#close"></use></svg>
        </div>
        <div class="svg-icon" v-else>
            <svg><use xlink:href="#burger"></use></svg>
        </div>
    </button>
</template>

<script setup lang="ts">
import {
    offcanvasToggle,
    offcanvasVisibleState,
} from "@site/composables/useOffcanvas";
</script>
