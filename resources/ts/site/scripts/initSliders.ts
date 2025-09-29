import Swiper from "swiper";
import { Pagination, Navigation } from "swiper/modules";

export function initReviewsList() {
    const slider = new Swiper(".js-reviews-slider", {
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
    });

    return slider;
}

export function initTeamSlider() {
    const slider = new Swiper(".js-team-slider", {
        modules: [Navigation],
        centeredSlides: true,
        speed: 600,
        spaceBetween: 0,
        slidesPerView: 1,
        initialSlide: 1,
        navigation: {
            prevEl: ".slider-prev",
            nextEl: ".slider-next",
        },
        breakpoints: {
            768: {
                slidesPerView: 3,
            },
            1280: {
                slidesPerView: 5,
            },
        },
    });

    return slider;
}

export function initGallerySlider() {
    const slider = new Swiper(".js-gallery-slider", {
        modules: [Pagination],
        centeredSlides: true,
        speed: 600,
        slidesPerView: "auto",
        spaceBetween: 8,
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

export function initCatalogMainSlider() {
    const slider = new Swiper(".js-catalog-main-slider", {
        modules: [Navigation],
        speed: 800,
        slidesPerView: 1,
        navigation: {
            prevEl: ".js-catalog-main-slider-prev",
            nextEl: ".js-catalog-main-slider-next",
        },
    });

    return slider;
}


