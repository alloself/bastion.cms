<template>
  <v-layout>
    <navigation-drawer />
    <top-bar />
    </е-bar>
    <app-view>
      <router-view v-slot="{ Component, route }">
        <component :is="Component" :key="route.name" />
      </router-view>
    </app-view>
    <modal-drawer />
  </v-layout>
</template>

<script lang="ts" setup>
import AppView from '@admin/widgets/AppView.vue';
import { onBeforeUnmount, onMounted } from "vue";
import { TopBar } from '@admin/features/top-bar';
import { NavigationDrawer } from '@admin/features/navigation-drawer';
import { ModalDrawer } from '@admin/features/modal-drawer';

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