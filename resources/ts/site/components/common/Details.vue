<template>
    <div v-cloak class="app-details" :class="{ 'is-open': state.isOpen }">
        <div class="app-details__title" :class="titleClass">
            <slot name="title" :toggle="onToggle"></slot>
            <div
                v-if="props.arrowVisible && slots.content"
                class="app-details__arrow"
                @click="onToggle"
            >
                <div class="svg-icon">
                    <svg><use xlink:href="#arrow-down"></use></svg>
                </div>
            </div>
        </div>
        <div class="app-details__content" v-if="slots.content">
            <div style="min-height: 0">
                <slot name="content"></slot>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onBeforeMount, reactive } from "vue";

const slots = defineSlots<{
    content: string;
    title: string;
}>();

const props = withDefaults(
    defineProps<{
        open?: boolean;
        arrowVisible?: boolean;
        titleClass?: string;
    }>(),
    {
        titleClass: "",
        arrowVisible: true,
        open: false,
    }
);

const state = reactive({
    isOpen: false,
});

function onToggle() {
    state.isOpen = !state.isOpen;
}

onBeforeMount(() => {
    state.isOpen = props.open;
});
</script>

<style lang="scss">
.app-details {
    &__title {
        @apply flex
            items-center
            select-none;
    }

    &__arrow {
        @apply flex-none 
            ml-auto 
            pl-1
            text-dark 
            text-opacity-40;

        .svg-icon {
            @apply cursor-pointer
                transition-all
                duration-300
                relative
                before:content-['']
                before:absolute
                before:-inset-[5px];
        }
    }

    &__content {
        @apply overflow-hidden
            grid
            transition-all
            duration-300
            text-base
            leading-normal;
        grid-template-rows: 0fr;
    }

    &.is-open {
        .app-details__arrow .svg-icon {
            @apply rotate-180;
        }

        .app-details__content {
            grid-template-rows: 1fr;
        }
    }
}
</style>
