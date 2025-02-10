<template>
    <v-navigation-drawer v-model="showNavigationDrawer">
        <v-list density="compact" nav>
            <v-list-item
                nav
                v-for="item in items"
                :key="item.key"
                :prepend-icon="item.icon"
                link
                :title="item.title"
                :to="{ name: item.to }"
            ></v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>

<script lang="ts" setup>
import { storeToRefs } from "pinia";
import { useNavigationDrawerStore } from "../store";
import { modules } from "@admin/shared/modules";
import { computed } from "vue";
import { capitalize, sortBy } from "lodash";

const navigationDrawerStore = useNavigationDrawerStore();
const { showNavigationDrawer } = storeToRefs(navigationDrawerStore);

const items = computed(() => {
    const array = sortBy(modules, ["title"])
        .map((item) => {
            return { ...item, to: `${capitalize(item.key)}List` };
        })
        .filter(({ showInNavigation }) => showInNavigation);

    return array;
});
</script>
