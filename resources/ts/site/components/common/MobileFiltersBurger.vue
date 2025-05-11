<template>
    <div class="flex items-center cursor-pointer" @click="onFiltersBurgerClick">
        <div class="svg-icon text-brand mr-2">
            <svg><use xlink:href="#filters"></use></svg>
        </div>
        <div class="text-[16px] tracking-[-0.32px] font-semibold">
            Фильтры
            <span v-if="state.filtersCount > 0"
                >({{ state.filtersCount }})</span
            >
        </div>
    </div>
</template>

<script setup lang="ts">
import { offcanvasToggle } from "../../composables/useOffcanvas";
import { reactive, onMounted } from "vue";

const state = reactive({
    filtersCount: 0,
});

function countActiveFilters() {
    if ("URLSearchParams" in window) {
        const url = new URL(window.location.href);
        const params = url.searchParams;

        // Считаем все параметры, начинающиеся с "filter_"
        let count = 0;
        for (const [key, value] of params.entries()) {
            if (key.includes("filter_") && value) {
                count++;
            }
        }

        state.filtersCount = count;
    }
}

function onFiltersBurgerClick() {
    offcanvasToggle("mobileCatalogFilters");
}

onMounted(() => {
    countActiveFilters();
});
</script>
