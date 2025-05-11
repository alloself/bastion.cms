<template>
    <div class="flex flex-col gap-5 lg:flex-row">
        <div class="lg:w-[320px] lg:flex-none">
            <div
                class="flex items-center font-semibold justify-between mb-2 pb-2 border-b border-neutral border-opacity-50"
            >
                <geolocation></geolocation>
                <div
                    class="flex gap-1 font-semibold text-[14px] leading-[1.1] text-dark text-opacity-40"
                >
                    <div>{{ currentLocation.pickUpPoints?.length || 0 }}</div>
                    <div>из</div>
                    <div>{{ allPickUpPointsLength }}</div>
                </div>
            </div>
            <div class="overflow-y-auto pr-2 max-h-[215px] md:max-h-[242px] xl:max-h-[440px]">
                <div
                    v-for="(point, pointIndex) in currentLocation.pickUpPoints"
                    :key="pointIndex"
                    class="transition-all duration-300 cursor-pointer py-4 border-b border-dark border-opacity-10 hover:border-opacity-100"
                    @click="onPointClick(point)"
                >
                    <div
                        class="opacity-80 text-[14px] mb-2 font-semibold leading-[1.1] tracking-[-0.28px] md:text-[20px]"
                    >
                        {{ point.address }}
                    </div>
                    <div
                        class="opacity-80 text-[14px] leading-[1.1] tracking-[-0.28px] md:text-[16px]"
                    >
                        {{ point.workHours }}
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:flex-1 lg:min-w-0">
            <div id="pickup-points-map" style="height: 480px; width: 100%"></div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, watch } from 'vue'
import {
    IPickUpPoint,
    allPickUpPointsLength,
    currentLocation,
    currentPickUpPoint,
} from '@site/composables/useGeoLocation'
import { toast } from '@site/composables/useToast'
import { modalClose } from '@site/composables/useModal'

const props = withDefaults(
    defineProps<{
        showMessage?: boolean
        selectPickUpPoint?: boolean
    }>(),
    {
        showMessage: true,
        selectPickUpPoint: false,
    }
)

let map: Record<any, any> = {}

watch(currentLocation, () => {
    addMapMarkersByLocation()
})

function addMapMarkersByLocation() {
    if (!map.constructor?.name) {
        return
    }

    map.geoObjects.removeAll()

    if (currentLocation.value.pickUpPoints.length === 0 && props.showMessage) {
        toast.show('error', `В городе ${currentLocation.value.city} не найдено пунктов выдачи`)
    }

    map.setCenter(currentLocation.value.latlng, 10, {
        duration: 400,
        checkZoomRange: true,
    })

    currentLocation.value.pickUpPoints?.forEach((point) => {
        const markerPoint = new ymaps.Placemark(
            point.latlng,
            {
                balloonContent: `
                <div class="font-base font-semibold text-[14px] ${
                    props.selectPickUpPoint ? 'cursor-pointer' : ''
                }">
                    <div class="mb-2.5">${point.address}</div>
                    ${
                        props.selectPickUpPoint
                            ? `<div class="inline-block underline text-brand js-select-point">Выбрать пункт</div>`
                            : ''
                    }
                </div>
            `,
                iconCaption: '',
            },
            {
                iconLayout: 'default#imageWithContent',
                iconImageOffset: [-15, -15],
                iconImageHref: '/public/img/geo-marker.svg',
                iconImageSize: [30, 30],
                balloonCloseButton: false,
                hideIconOnBalloonOpen: false,
            }
        )

        // markerPoint.events.add(['click'], function(event: any) {
        //     map.setCenter(point.latlng, 16, {
        //         duration: 400,
        //         checkZoomRange: true
        //     })
        // })

        markerPoint.balloon.events.add(['click'], function (event: any) {
            try {
                if (
                    event.originalEvent.target.properties._data.balloonContent.includes(
                        'js-select-point'
                    )
                ) {
                    currentPickUpPoint.value = point
                    modalClose('pickUpPointsModal')
                    toast.show('success', `Выбран пункт самовывоза - ${point.address}`, {
                        duration: 10000,
                    })
                }
            } catch (error) {
                console.log(error)
            }
        })

        map.geoObjects.add(markerPoint)
    })

    if (map.geoObjects.getBounds()) {
        map.setBounds(map.geoObjects.getBounds())
    }
}

function onPointClick(point: IPickUpPoint) {
    if (!map.constructor?.name) {
        return
    }

    map.setCenter(point.latlng, 16, {
        duration: 400,
        checkZoomRange: true,
    })
}

onMounted(() => {
    ymaps.ready(() => {
        map = new ymaps.Map('pickup-points-map', {
            center: currentLocation.value.latlng,
            zoom: 10,
            controls: [],
        })

        addMapMarkersByLocation()
    })
})
</script>
