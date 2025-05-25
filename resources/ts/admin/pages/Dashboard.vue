<template>
  <v-layout>
    <navigation-drawer />
    <top-bar />
    </е-bar>
    <app-view>
      <router-view v-slot="{ Component, route }">
        <component :is="Component" :key="route.name" />
      </router-view>

      <!--<Screen v-for="(screen, index) in screenStore.screens" :key="screen.id" :screen="screen" />-->
    </app-view>
    <modal-drawer />
  </v-layout>
</template>

<script lang="ts" setup>
import AppView from '@admin/widgets/AppView.vue';
import Screen from '../features/screen/components/Screen.vue';
import { onBeforeUnmount, onMounted } from "vue";
import TopBar from '@admin/features/top-bar/components/TopBar.vue';
import NavigationDrawer from '@admin/features/navigation-drawer/components/NavigationDrawer.vue';
import ModalDrawer from '@admin/features/modal-drawer/components/ModalDrawer.vue';
import { useScreenStore } from '../features/screen';

let HTMLDOMElement: HTMLHtmlElement | null;


const screenStore = useScreenStore()


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