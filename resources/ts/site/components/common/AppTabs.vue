<template>
    <div class="app-tabs">
        <div class="app-tabs__titles flex flex-wrap gap-1 mb-8 md:gap-2 md:mb-12">
            <button
                v-for="(button, buttonIndex) in items"
                type="button" 
                class="app-button app-button--secondary text-dark font-semibold normal-case text-[14px] py-2 px-4 md:py-2.5 md:px-5 md:text-[20px]"
                :key="buttonIndex"
                :class="state.activeKey === button.key ? 'text-opacity-100': 'text-opacity-50'"
                @click="onChangeTab(button.key)"
            >{{ button.label }}</button>
        </div>
        <div class="app-tabs__bodies">
            <template v-for="(tab, tabIndex) in items" :key="tabIndex">
                <Transition name="tabs">
                    <div
                        class="app-tabs__bodies-item" 
                        v-show="tab.key === state.activeKey"
                    >
                        <slot :name="tab.key"></slot>
                    </div>
                </Transition>
            </template>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, reactive } from 'vue'

const props = withDefaults(
    defineProps<{
        activeKey?: string | number,
        items: {
            label: string,
            key: string | number,
        }[]
    }>(),
    {   
        activeKey: '',
        items: () => [],
    }
)

const state = reactive<{
    activeKey: string | number
}>({
    activeKey: ''
})

function onChangeTab(key: string | number) {
    state.activeKey = key
}

onMounted(() => {
    state.activeKey = props.activeKey || props.items[0].key
})
</script>

<style lang="scss">
// enter
.tabs-enter-from {
    opacity: 0;
    transform: translateY(40px);
}
.tabs-enter-active {
    transition: all 0.4s;
}
.tabs-enter-to {
    transform: translateY(0);
    opacity: 1;
}
// leave
.tabs-leave-from {
}
.tabs-leave-active {
    display: none;
}
.tabs-leave-to {
}
</style>