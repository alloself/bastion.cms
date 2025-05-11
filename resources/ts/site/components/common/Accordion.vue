<template>
    <div class="app-acc" :class="{ 'is-open': state.isOpen }">
        <div class="app-acc__top" @click="onToggle">
            <div class="app-acc__title">
                <slot name="title"></slot>
            </div>
            <div class="rounded-icon transition-all duration-300 pointer">
                <div class="svg-icon">
                    <svg><use xlink:href="#arrow-down"></use></svg>
                </div>
            </div>
        </div>
        <div class="app-acc__body">
            <div style="min-height: 0">
                <slot name="body" :isOpen="state.isOpen" :toggle="onToggle"></slot>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { onBeforeMount, reactive } from 'vue'

const props = defineProps<{
    open?: boolean
}>()

const state = reactive({
    isOpen: false,
})

function onToggle() {
    state.isOpen = !state.isOpen
}

onBeforeMount(() => {
    if (state.isOpen !== props.open) {
        state.isOpen = props.open
    }
})
</script>

<style lang="scss">
.app-acc {
    @apply mb-3
        border-b
        border-neutral
        transition-all
        duration-300
        will-change-contents;

    &__top {
        @apply select-none
            cursor-pointer
            flex
            items-center
            py-2.5
            transition-all
            duration-300;
    }

    &__title {
        @apply pr-2
            flex-1
            text-[20px]
            leading-[1.0]
            tracking-[-0.96px]
            font-semibold
            md:text-[35px]
            lg:text-[48px];
    }

    &__body {
        @apply overflow-hidden
            grid
            transition-all
            duration-300
            text-base
            leading-normal;
        grid-template-rows: 0fr;
    }

    .rounded-icon {
        @apply w-8
            h-8
            md:w-11
            md:h-11;
    }

    &.is-open {
        .app-acc__top {
            @apply mb-5;
        }

        .rounded-icon {
            @apply rotate-180 ring-1 ring-brand border-brand;
        }

        .app-acc__body {
            grid-template-rows: 1fr;
            @apply pb-8;
        }
    }
}
</style>
