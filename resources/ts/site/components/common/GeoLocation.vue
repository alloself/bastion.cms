<template>
    <div class="flex items-center">
        <div class="svg-icon mr-[2px]" :class="iconClass">
            <svg><use xlink:href="#geo"></use></svg>
        </div>
        <div>Санкт-Петербург</div>
    </div>
</template>

<script setup lang="ts">
import { currentLocation } from '@site/composables/useGeoLocation'
import { modalToggle } from '@site/composables/useModal'

const props = withDefaults(
    defineProps<{
        iconClass?: string
    }>(),
    {
        iconClass: 'text-brand',
    }
)

function clickHandler() {
    modalToggle('geoLocationModal')
}
</script>
