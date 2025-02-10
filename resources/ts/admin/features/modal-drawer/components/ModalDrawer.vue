<template>
    <v-navigation-drawer
        :width="drawerWidth"
        disable-resize-watcher
        location="right"
        :model-value="modalStore.show"
        @update:model-value="onClose"
        class="modals-wrapper"
    >
        <component
            class="modal"
            v-for="(modal, index) in modals"
            :is="modal.component"
            v-bind="modal.props"
            v-on="modal.actions"
            :key="index"
        ></component>
    </v-navigation-drawer>
</template>

<script setup lang="ts">
import { storeToRefs } from "pinia";
import { computed } from "vue";
import { useDisplay } from "vuetify";
import { useModalDrawerStore } from "../store";

const display = useDisplay();
const modalStore = useModalDrawerStore();

const drawerWidth = computed(() => {
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
