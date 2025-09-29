<template>
    <button
        class="app-button app-button--square"
        type="button"
        :class="[
            offcanvasVisibleState['mobileMenu']
                ? 'app-button--primary'
                : 'app-button--secondary',
        ]"
        @click.prevent="offcanvasToggle('mobileMenu')"
    >
        <div class="svg-icon size-5" v-if="offcanvasVisibleState['mobileMenu']">
            <svg><use xlink:href="#close"></use></svg>
        </div>
        <div class="svg-icon size-5" v-else>
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
