<template>
    <ul class="space-y-2">
        <li
            v-for="(location, locationIndex) in locationsList"
            :key="locationIndex"
            class="text-[16px] leading-[1.1] font-semibold"
            :class="{ 'text-brand': location.city === currentLocation.city }"
        >
            <a href="#" class="decor-link" @click.prevent="changeLocation(location)">{{
                location.city
            }}</a>
        </li>
    </ul>
</template>

<script setup lang="ts">
import {
    IGeoLocation,
    currentLocation,
    locationsList,
} from '@site/composables/useGeoLocation'
import { modalClose } from '@site/composables/useModal'

function changeLocation(location: IGeoLocation) {
    currentLocation.value = location
    modalClose('geoLocationModal')
}
</script>
