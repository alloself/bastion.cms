import { Swiper } from 'swiper'
import { onBeforeUnmount, onMounted } from 'vue'

export function useSwiper(initSliderFunc: () => Swiper | Swiper[], timeout: number = 0) {
    let swiper: Swiper | Swiper[] | null = null

    onMounted(() => {
        setTimeout(() => {
            swiper = initSliderFunc()
        }, timeout)
    })

    onBeforeUnmount(() => {
        if (Array.isArray(swiper)) {
            swiper.forEach(swiper => swiper.destroy())
        } else {
            swiper?.destroy()
        }
    })
}