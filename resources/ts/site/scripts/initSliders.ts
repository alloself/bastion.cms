import { Swiper } from "swiper";
import { Pagination, Navigation, Autoplay } from "swiper/modules";
import { randomNumberBetween } from "../utils/utils";

export function initArticlesItemSlider(): Swiper | Swiper[] | undefined {
    const sliderElement = document.querySelector(".js-article-item-slider");
    if (!sliderElement) {
        return;
    }

    const slider = new Swiper(sliderElement as HTMLElement, {
        modules: [Pagination],
        speed: 600,
        spaceBetween: 20,
        slidesPerView: 1,
        pagination: {
            clickable: true,
            el: ".slider-pagination",
            renderBullet(index: number, className: string) {
                return `
                    <div class="slider-pagination__item ${className}"></div>
                `;
            },
        },
    });

    return slider;
}

export function initTapeSlider(): Swiper | Swiper[] | undefined {
    const sliderElement = document.querySelector(".js-tape-slider");
    if (!sliderElement) {
        return;
    }
    
    const slider = new Swiper(sliderElement as HTMLElement, {
        slidesPerView: "auto",
        speed: 800,
        modules: [Autoplay],
        spaceBetween: 0,
        centeredSlides: true,
        centeredSlidesBounds: true,
        loop: true,
        autoplay: {
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },
    });

    return slider;
}

export function initProductItemSlider(): Swiper | Swiper[] | undefined {
    const sliderElement = document.querySelector(".js-product-item-slider");
    if (!sliderElement) {
        return;
    }

    const slider = new Swiper(sliderElement as HTMLElement, {
        modules: [Pagination, Navigation],
        speed: 600,
        spaceBetween: 20,
        slidesPerView: 1,
        navigation: {
            prevEl: ".slider-prev",
            nextEl: ".slider-next",
        },
        pagination: {
            clickable: true,
            el: ".slider-pagination",
            renderBullet(index: number, className: string) {
                return `
                    <div class="slider-pagination__item ${className}"></div>
                `;
            },
        },
    });

    return slider;
}

export function initSlideShow(): Swiper | Swiper[] | undefined {
    const sliderElement = document.querySelector(".js-slide-show");
    if (!sliderElement) {
        return;
    }

    const slider = new Swiper(sliderElement as HTMLElement, {
        modules: [Pagination, Navigation],
        speed: 600,
        spaceBetween: 0,
        slidesPerView: 1,
        navigation: {
            prevEl: ".slider-prev",
            nextEl: ".slider-next",
        },
        pagination: {
            clickable: true,
            el: ".slider-pagination",
            renderBullet(index: number, className: string) {
                return `
                    <div class="slider-pagination__item ${className}"></div>
                `;
            },
        },
        // for dev
        on: {
            init(swiper) {
                swiper.slideTo(randomNumberBetween(1, 6) - 1);
            },
        },
    });

    return slider;
}

export function initMainBannersSlider(): Swiper | Swiper[] | undefined {
    const sliderElement = document.querySelector(".js-main-banner-slider");
    if (!sliderElement) {
        return;
    }

    const slider = new Swiper(sliderElement as HTMLElement, {
        modules: [Pagination, Navigation],
        speed: 600,
        spaceBetween: 0,
        slidesPerView: 1,
        simulateTouch: false,
        navigation: {
            prevEl: ".slider-prev",
            nextEl: ".slider-next",
        },
        pagination: {
            clickable: true,
            el: ".slider-pagination",
            renderBullet(index: number, className: string) {
                return `
                    <div class="slider-pagination__item ${className}"></div>
                `;
            },
        },
    });

    return slider;
}
