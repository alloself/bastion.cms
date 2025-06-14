// styles
import "swiper/css";
import "cooltipz-css";
import "baguettebox.js/dist/baguetteBox.min.css";

// scripts
import {
    initArticlesItemSlider,
    initTapeSlider,
    initProductItemSlider,
    initSlideShow,
    initMainBannersSlider,
} from "./scripts/initSliders";
import { initScrollAnimate } from "./scripts/initScrollAnimate";
import { initContactsMap } from "./scripts/initYandexMap";
import { initThreeObjects } from "./scripts/initThreeObjects";
import { initGallery } from "./scripts/initGallery";
import { applyNightModeToWorkClocks, defaultWorkingHours } from "./utils/workClocksNightMode";
import type { WorkingHoursConfig } from "./utils/workClocksNightMode";

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

document.addEventListener("DOMContentLoaded", () => {
    try {
        initArticlesItemSlider();
        initProductItemSlider();
        initSlideShow();
        initMainBannersSlider();
        initTapeSlider();
        initGallery();

        // Можно использовать дефолтную конфигурацию
        applyNightModeToWorkClocks();
        
        // Или передать кастомную конфигурацию:
        // const customWorkingHours: WorkingHoursConfig = {
        //     0: { start: "12:00", end: "18:00", isWorkingDay: false }, // воскресенье
        //     1: { start: "09:00", end: "21:00", isWorkingDay: true },  // понедельник
        //     2: { start: "09:00", end: "21:00", isWorkingDay: true },  // вторник
        //     3: { start: "09:00", end: "21:00", isWorkingDay: true },  // среда
        //     4: { start: "09:00", end: "21:00", isWorkingDay: true },  // четверг
        //     5: { start: "09:00", end: "21:00", isWorkingDay: true },  // пятница
        //     6: { start: "10:00", end: "16:00", isWorkingDay: true },  // суббота
        // };
        // applyNightModeToWorkClocks(customWorkingHours);
    } catch (e) {
        console.log(e);
    }
});

window.onload = () => {
    try {
        initScrollAnimate();
        if (document.querySelector("[data-map]")) {
            initContactsMap();
            console.log("Запущена инициализация карт");
        }
        initThreeObjects();
    } catch (e) {
        console.log(e);
    }
};
