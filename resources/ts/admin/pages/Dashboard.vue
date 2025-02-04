<template>
    <v-layout>
        <!--<app-navigation-drawer></app-navigation-drawer>-->
        <app-bar>
            
        </app-bar>
        <app-view>
            <router-view v-slot="{ Component, route }">
                <component :is="Component" :key="route.name" />
            </router-view>
        </app-view>
        <!--<app-modals-drawer></app-modals-drawer>-->
    </v-layout>
</template>

<script lang="ts" setup>
import AppBar from '@admin/widgets/AppBar.vue';
import AppView from '@admin/widgets/AppView.vue';
import { onBeforeUnmount, onMounted } from "vue";

let HTMLDOMElement: HTMLHtmlElement | null;

onMounted(() => {
    HTMLDOMElement = document.querySelector("html");
    if (HTMLDOMElement) {
        HTMLDOMElement.style.overflow = "hidden";
    }
});
onBeforeUnmount(() => {
    if (HTMLDOMElement) {
        HTMLDOMElement.style.overflow = "auto";
    }
});

</script>