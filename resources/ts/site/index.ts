// styles
import "swiper/css";
import "cooltipz-css";
import "@/scss/site/index.scss";
import "baguettebox.js/dist/baguetteBox.min.css";

// scripts
import {
    initCatalogMainSlider,
    initGallerySlider,
    initReviewsList,
    initTeamSlider,
} from "@site/scripts/initSliders";
import { initGallery } from "@site/scripts/initGallery";

// composables
import { createApp, onMounted } from "vue";

// directives
import ModalDirective from "@site/directives/Modal";

// components
import MobileMenuBurger from "@site/components/common/MobileMenuBurger.vue";
import Offcanvas from "@site/components/common/Offcanvas.vue";
import Modal from "@site/components/common/AppModal.vue";
import Notifications from "@site/components/common/Notifications.vue";
import CallbackForm from "@site/components/forms/CallbackForm.vue";
import Accordion from "@site/components/common/Accordion.vue";

const app = createApp({
    setup() {
        onMounted(async () => {
            initReviewsList();
            initTeamSlider();
            initGallerySlider();
            initGallery();
            initCatalogMainSlider();
        });
    },
})
    .directive("modal-call", ModalDirective)
    .component("mobilemenutrigger", MobileMenuBurger)
    .component("appmodal", Modal)
    .component("notifications", Notifications)
    .component("callbackform", CallbackForm)
    .component("offcanvas", Offcanvas)
    .component('appaccodion', Accordion)

document.addEventListener("DOMContentLoaded", () => {
    app.mount("#app");
});
