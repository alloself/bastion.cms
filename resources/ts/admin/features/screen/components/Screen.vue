<template>
  <v-card outlined rounded="0" flat class="h-100 d-flex flex-column overflow-auto w-100">
    <v-card-title class="d-flex align-center px-2" :class="{ 'ga-2': screen.tabs.length }">
      <v-slide-group v-model="selectedTab" show-arrows>
        <v-slide-group-item v-for="(tab, index) in screen.tabs" :key="index" v-slot="{ isSelected, toggle }">
          <v-chip class="screen-chip mr-2" :color="isSelected ? 'primary' : ''" size="small" @click="toggle" label
            closable @click:close.prevent="screenStore.removeTab(screenIndex, index)">
            {{ tab }}
          </v-chip>
        </v-slide-group-item>
      </v-slide-group>
      <v-btn size="x-small" icon="mdi-plus" variant="flat" @click="onAddClick" />
      <v-spacer></v-spacer>
      <v-btn size="x-small" icon="mdi-dock-window" variant="flat" @click="screenStore.addScreen" />
      <v-btn v-if="screens.length > 1" size="x-small" icon="mdi-close" variant="flat"
        @click="screenStore.removeScreen(screenIndex)" />
    </v-card-title>
    <keep-alive>
      <v-card-text class="bg-surface-light px-0 py-0">
        <slot name="default"></slot>
      </v-card-text>
    </keep-alive>
    <v-card-actions v-if="slots.actions">
      <slot name="actions"></slot>
    </v-card-actions>
  </v-card>
  <v-divider vertical class="divider"></v-divider>
</template>

<script setup lang="ts">
import { ref, useSlots } from "vue";
import { useScreenStore, type IScreen } from "../store";
import { storeToRefs } from "pinia";

const { screen, screenIndex } = defineProps<{
  screen: IScreen;
  screenIndex: number;
}>();
const screenStore = useScreenStore();
const { screens } = storeToRefs(screenStore);

const onAddClick = () => {
  screenStore.addTab(screenIndex, 'v-text-field')
}

const selectedTab = ref(null);

const slots = useSlots();
</script>
