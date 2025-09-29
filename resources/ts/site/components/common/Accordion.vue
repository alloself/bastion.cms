<template>
    <div class="app-acc" :class="{ 'is-open': state.isOpen }">
        <div class="app-acc__top" @click="onToggle">
            <div class="app-acc__title">
                <slot name="title"></slot>
            </div>
            <div class="app-acc__arrow transition-all duration-300 pointer">
                <div class="svg-icon">
                    <svg><use xlink:href="#arrow-right"></use></svg>
                </div>
            </div>
        </div>
        <div class="app-acc__body">
            <div style="min-height: 0">
                <slot
                    name="body"
                    :isOpen="state.isOpen"
                    :toggle="onToggle"
                ></slot>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { onBeforeMount, reactive } from "vue";

const props = defineProps<{
    open?: boolean;
}>();

const state = reactive({
    isOpen: false,
});

function onToggle() {
    state.isOpen = !state.isOpen;
}

onBeforeMount(() => {
    if (state.isOpen !== props.open) {
        state.isOpen = props.open;
    }
});
</script>

<style lang="scss">
.app-acc {
    @apply border-b
        border-brand/20
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
        @apply pr-4
            flex-1;
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

    .app-acc__arrow {
        @apply w-12
            h-12
            items-center
            justify-center
            rounded-sm
            ring-1
            ring-brand/20
            hidden
            bg-white
            md:flex;

        .svg-icon {
            @apply fill-brand w-5 h-5;
        }
    }

    &.is-open {
        .app-acc__top {
            @apply mb-2;
        }

        .app-acc__arrow {
            @apply rotate-90;
        }

        .app-acc__body {
            grid-template-rows: 1fr;
            @apply pb-8;
        }
    }
}
</style>
