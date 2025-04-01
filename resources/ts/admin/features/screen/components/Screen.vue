<template>
    <v-card outlined rounded="0" flat class="h-100 d-flex flex-column w-100">
        <v-card-title
            class="d-flex align-center px-2"
            :class="{ 'ga-2': screen.tabs.length }"
        >
            <v-slide-group v-model="selectedTabId" show-arrows item-value="id">
                <v-slide-group-item
                    v-for="tab in screen.tabs"
                    :key="tab.id"
                    v-slot="{ isSelected, toggle }"
                >
                    <v-chip
                        class="screen-chip mr-2"
                        :color="isSelected ? 'primary' : ''"
                        size="small"
                        @click="toggle"
                        closable
                        @click:close.prevent="
                            () => screenStore.closeTab(tab.id)
                        "
                        label
                    >
                        {{ tab.title }}
                    </v-chip>
                </v-slide-group-item>
            </v-slide-group>

            <v-btn
                size="x-small"
                icon="mdi-plus"
                variant="flat"
                @click="onAddClick"
            />
            <v-spacer></v-spacer>
            <v-btn
                size="x-small"
                icon="mdi-dock-window"
                variant="flat"
                @click="addScreen"
            />
            <v-btn
                v-if="screenStore.screens.length > 1"
                size="x-small"
                icon="mdi-close"
                variant="flat"
                @click="onRemoveScreen"
            />
        </v-card-title>
        <router-view v-slot="{ Component, route }">
            <keep-alive>
                <component
                    :is="Component"
                    :key="route.fullPath"
                    v-if="isActiveTab(route.fullPath)"
                />
            </keep-alive>
        </router-view>
    </v-card>
    <v-divider vertical class="divider"></v-divider>
</template>

<script setup lang="ts">
import { ref, computed, useSlots } from "vue";
import { storeToRefs } from "pinia"; // если используется Pinia для других вещей, иначе удалить
import { useScreenStore } from "../store";
import { IScreen } from "@/ts/admin/shared/types";

const { screen } = defineProps<{
    screen: IScreen;
}>();

const slots = useSlots();

const screenStore = useScreenStore();

// Reactive binding для выбранной вкладки
const selectedTabId = computed({
    get: () => screen.activeTabId,
    set: (tabId: string) => {
        console.log(tabId);
        screenStore.activateTab(tabId);
    },
});

const isActiveTab = (fullPath: string) => {
    const activeTab = screen.tabs.find((tab) => tab.id === screen.activeTabId);
    return activeTab?.fullPath === fullPath;
};

const onAddClick = () => {
    // Пример добавления вкладки (здесь можно адаптировать под свои нужды)
    screenStore.openTab({
        fullPath: "/new-route",
        name: "New Route",
        meta: { title: "Новая вкладка" },
        params: {},
        query: {},
    } as any);
};

// Методы для управления экранами (если реализованы)
const addScreen = () => {
    screenStore.screens.push({
        id: crypto.randomUUID(),
        tabs: [],
        activeTabId: "",
    });
};

const onRemoveScreen = () => {
    screenStore.removeScreen(screen.id);
};
</script>

<style scoped>
.screen-chip {
    cursor: pointer;
}
</style>
