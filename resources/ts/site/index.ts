// styles
import "swiper/css";
import "cooltipz-css";
import "baguettebox.js/dist/baguetteBox.min.css";

// vue imports
// @ts-ignore
import { createApp } from "vue/dist/vue.esm-bundler.js";

// vue directives
import ScrollToDirective from "./directives/ScrollTo";
import ModalDirective from "./directives/Modal";

// vue global components
import Details from "./components/common/Details.vue";
import GeoLocation from "./components/common/GeoLocation.vue";
import GeoLocationList from "./components/common/GeoLocationList.vue";
import AppSearch from "./components/common/AppSearch.vue";
import Accordion from "./components/common/Accordion.vue";
import AppTabs from "./components/common/AppTabs.vue";
import Pagination from "./components/common/Pagination.vue";
import AppSort from "./components/common/AppSort.vue";
import CatalogFilter from "./components/common/CatalogFilter.vue";
import AppSelect from "./components/common/AppSelect.vue";
import MobileMenuBurger from "./components/common/MobileMenuBurger.vue";
import MobileFiltersBurger from "./components/common/MobileFiltersBurger.vue";
import CountButtons from "./components/common/CountButtons.vue";
import Offcanvas from "./components/common/Offcanvas.vue";
import OffcanvasClose from "./components/common/OffcanvasClose.vue";
import AppModal from "./components/common/AppModal.vue";
import PickUpPoints from "./components/common/PickUpPoints.vue";
import LoginForm from "./components/forms/LoginForm.vue";
import RegistrationForm from "./components/forms/RegistrationForm.vue";
import PasswordRecoveryForm from "./components/forms/PasswordRecoveryForm.vue";
import OrderForm from "./components/forms/OrderForm.vue";
import PersonalDataForm from "./components/forms/PersonalDataForm.vue";
import PersonalPasswordForm from "./components/forms/PersonalPasswordForm.vue";
import CallbackForm from "./components/forms/CallbackForm.vue";
import { useLiked } from "./composables/useLiked";
import { onMounted, ref } from "vue";
import { useCart } from "./composables/useCart";
import axios from "axios";

const app = createApp({
    setup() {
        const { liked, toggleLiked, getLikedIds } = useLiked();
        const { cart, getCart, addItemToCart } = useCart();

        const user = ref();

        onMounted(async () => {
            try {
                //await axios.get("/sanctum/csrf-cookie");
                //const { data } = await axios.get("/api/public/me");
                //user.value = data;
            } catch (e) {}

            liked.value = getLikedIds();
            const cartID = localStorage.getItem("cms:cart");
            if (cartID) {
                await getCart(cartID);
            }
        });

        return {
            liked,
            toggleLiked,
            getLikedIds,
            cart,
            addItemToCart,
        };
    },
})
    .directive("scroll-to", ScrollToDirective)
    .directive("modal-call", ModalDirective)
    .component("appdetails", Details)
    .component("countbuttons", CountButtons)
    .component("loginform", LoginForm)
    .component("callbackform", CallbackForm)
    .component("registrationform", RegistrationForm)
    .component("passwordrecoveryform", PasswordRecoveryForm)
    .component("personaldataform", PersonalDataForm)
    .component("personalpasswordform", PersonalPasswordForm)
    .component("orderform", OrderForm)
    // .component('OrderDetails', OrderDetails)
    .component("appsearch", AppSearch)
    .component("geolocation", GeoLocation)
    .component("geolocationlist", GeoLocationList)
    .component("accordion", Accordion)
    .component("apptabs", AppTabs)
    .component("pagination", Pagination)
    .component("appsort", AppSort)
    .component("appselect", AppSelect)
    .component("catalogfilter", CatalogFilter)
    .component("mobilefiltersburger", MobileFiltersBurger)
    .component("mobilemenuburger", MobileMenuBurger)
    .component("offcanvas", Offcanvas)
    .component("offcanvasclose", OffcanvasClose)
    .component("appmodal", AppModal)
    .component("pickuppoints", PickUpPoints);

app.mount("#app");

// Критические скрипты загружаем сразу после монтирования Vue
document.addEventListener("DOMContentLoaded", () => {
    // Только самые критические функции
    loadCriticalScripts();
});

// Все остальное загружаем после полной загрузки страницы
window.addEventListener('load', () => {
    // Используем максимальную задержку для предотвращения блокировки
    if ('requestIdleCallback' in window) {
        requestIdleCallback(loadNonCriticalScripts, { timeout: 5000 });
    } else {
        setTimeout(loadNonCriticalScripts, 1000);
    }
});

// Критические скрипты (только UI)
async function loadCriticalScripts() {
    try {
        const { applyNightModeToWorkClocks } = await import("./utils/workClocksNightMode");
        applyNightModeToWorkClocks();
    } catch (e) {
        console.log(e);
    }
}

// Некритические скрипты (слайдеры, галереи, анимации)
async function loadNonCriticalScripts() {
    try {
        // Загружаем слайдеры и галереи
        await loadSlidersAndGallery();
        
        // Загружаем анимации с дополнительной задержкой
        setTimeout(loadAnimations, 500);
        
        // Загружаем тяжелые 3D объекты в самом конце
        setTimeout(loadHeavyAssets, 2000);
        
    } catch (e) {
        console.log(e);
    }
}

// Слайдеры и галереи
async function loadSlidersAndGallery() {
    try {
        const [
            { initArticlesItemSlider, initTapeSlider, initProductItemSlider, initSlideShow, initMainBannersSlider },
            { initGallery }
        ] = await Promise.all([
            import("./scripts/initSliders"),
            import("./scripts/initGallery")
        ]);
        
        // Инициализируем только если элементы присутствуют
        requestAnimationFrame(async () => {
            initArticlesItemSlider();
            initProductItemSlider();
            initSlideShow();
            initMainBannersSlider();
            initTapeSlider();
            
            // Асинхронная инициализация галереи
            await initGallery();
        });
    } catch (e) {
        console.log(e);
    }
}

// Анимации скролла
async function loadAnimations() {
    try {
        const { initScrollAnimate } = await import("./scripts/initScrollAnimate");
        
        // Используем requestIdleCallback для анимаций
        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                initScrollAnimate();
            });
        } else {
            setTimeout(initScrollAnimate, 100);
        }
    } catch (e) {
        console.log(e);
    }
}

// Самые тяжелые ресурсы загружаем в последнюю очередь
async function loadHeavyAssets() {
    try {
        // Карты загружаем только при наличии элементов и с максимальной задержкой
        if (document.querySelector("[data-map]")) {
            setTimeout(async () => {
                try {
                    const { initContactsMap } = await import("./scripts/initYandexMap");
                    await initContactsMap();
                    console.log("Карты инициализированы");
                } catch (e) {
                    console.log("Ошибка загрузки карт:", e);
                }
            }, 1000);
        }

        // Оптимизированные Three.js объекты загружаем в самом конце с проверкой видимости
        if (document.querySelector("[class*='three-lamp']")) {
            setTimeout(async () => {
                try {
                    const { initOptimizedThreeObjects } = await import("./scripts/initOptimizedThreeObjects");
                    await initOptimizedThreeObjects();
                    console.log("Оптимизированные Three.js объекты инициализированы");
                } catch (e) {
                    console.log("Ошибка загрузки оптимизированных Three.js:", e);
                    
                    // Fallback к обычной версии при ошибке
                    try {
                        const { initThreeObjects } = await import("./scripts/initThreeObjects");
                        await initThreeObjects();
                        console.log("Fallback: обычные Three.js объекты инициализированы");
                    } catch (fallbackError) {
                        console.log("Ошибка fallback Three.js:", fallbackError);
                    }
                }
            }, 3000);
        }
    } catch (e) {
        console.log(e);
    }
}
