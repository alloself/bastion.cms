<template>
    <v-app-bar order="-1" :elevation="0" :border="true" class="app-bar" flat density="compact">
        <v-app-bar-nav-icon size="small" @click="toggle" />
        <Logo />
        <v-app-bar-title>Bastion.CMS</v-app-bar-title>
        <template #append>
            <v-menu v-if="user" v-model="userMenu" :close-on-content-click="false" location="bottom">
                <template #activator="{ props }">
                    <v-btn icon v-bind="props" size="small">
                        <v-icon>mdi-account-circle</v-icon>
                    </v-btn>
                </template>
                <v-card min-width="300">
                    <v-list>
                        <v-list-subheader>Пользователь</v-list-subheader>
                        <v-list-item :title="user?.name" :subtitle="user?.email">
                        </v-list-item>
                    </v-list>
                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-btn color="primary" variant="outlined" size="small" block @click="onLogout">
                            Выйти
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-menu>
        </template>
        <template #extension v-if="slots.extension">
            <slot name="extension"></slot>
        </template>
    </v-app-bar>
</template>

<script setup lang="ts">
import { Logo } from "@admin/shared/components";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@admin/entities/user";
import { useNavigationDrawerStore } from "@admin/features/navigation-drawer";
import { storeToRefs } from "pinia";

const userStore = useUserStore();
const { toggle } = useNavigationDrawerStore();
const router = useRouter();

const userMenu = ref(false);
const { user } = storeToRefs(userStore);

const onLogout = () => {
    userStore.logout();
    router.push({ name: "Login" });
};

const slots = defineSlots<{
    extension: unknown;
}>();
</script>

<style lang="scss">
.app-bar {
    border-left: none;
    border-right: none;
}
</style>
