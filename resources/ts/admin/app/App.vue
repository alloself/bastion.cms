<template>
    <suspense>
        <v-app>
            <router-view v-slot="{ Component }">
                <component :is="Component" />
            </router-view>
            <notification
                v-for="(notification, index) in notifications"
                :key="index"
                :notification="notification"
                :style="getOffsetStyle(index)"
                @onCancel="closeAlert(index)"
            >
            </notification>
        </v-app>
    </suspense>
</template>

<script setup lang="ts">
import Notification from '@admin/features/notifications/components/Notification.vue';
import { useNotificationsStore } from "@admin/features/notifications/store/notificationsStore";

const { notifications, closeAlert } = useNotificationsStore();

const getOffsetStyle = (index: number) => {
    return {
        transform: `translateY(${index * 64}px)`,
    };
};
</script>
