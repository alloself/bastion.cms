<template>
    <v-navigation-drawer
        :width="drawerWidth"
        disable-resize-watcher
        location="right"
        :model-value="modalStore.show"
        @update:model-value="onClose"
        @mousedown.middle.prevent="toggleFullScreen"
        class="modals-wrapper"
    >
        <component
            v-for="(modal, index) in modals"
            :is="modal.component"
            detailClass="modal"
            v-bind="{ ...$attrs, ...modal.props, ...modal.actions }"
            :key="index"
        ></component>
    </v-navigation-drawer>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { computed, ref } from "vue";
import { useDisplay } from "vuetify";
import { useModalDrawerStore } from "../store";

const display = useDisplay();
const modalStore = useModalDrawerStore();
const fullScreen = ref(false);

const drawerWidth = computed(() => {
    if (fullScreen.value) {
        return display.width.value;
    }
    return display.width.value > 500
        ? display.width.value / 2
        : display.width.value;
});

const { modals } = storeToRefs(modalStore);

modalStore.$subscribe((_, { modals }) => {
    if (modals.length === 0) {
        modalStore.show = false;
    }
});

const onClose = () => {
    modalStore.modals = [];
    modalStore.show = false;
};

const toggleFullScreen = () => {
    fullScreen.value = !fullScreen.value;
};

</script>
<style lang="scss">
.modals-wrapper {
    position: relative;
    overflow-x: hidden;

    .modal {
        position: absolute;
        left: 0;
        top: 0;
        overflow-x: hidden !important;
    }
}
</style>
