<template>
    <div class="flex items-center gap-4 text-dark select-none">
        <div class="rounded-icon text-dark text-[18px] font-bold" :class="buttonsClass" @click.stop.prevent="decrease">-</div>
        <div class="text-[20px] font-bold text-center min-w-[22px]">{{ state.count }}</div>
        <div class="rounded-icon text-dark text-[18px] font-bold" :class="buttonsClass" @click.stop.prevent="increase">+</div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

const props = withDefaults(
    defineProps<{
        buttonsClass?: string
    }>(),
    {
        buttonsClass: ''
    }
)

const state = reactive({
    count: 1
})

function increase() {
    state.count++
}

function decrease() {
    if (state.count > 1) {
        state.count--
    }
}
</script>