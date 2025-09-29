<template>
    <div
        class="notifications-container"
        :class="`notifications-container--${useNotifications.config.align}`"
    >
        <TransitionGroup name="notifications-list">
            <div
                class="notifications-item"
                v-for="(item, itemIndex) in useNotifications.list"
                :key="itemIndex"
                :class="`notifications-item--${item.type}`"
                @click.stop="
                    () => (item.id ? useNotifications.remove(item.id) : '')
                "
            >
                <div v-if="item.icon" class="notifications-item__icon">
                    <div class="svg-icon size-5">
                        <svg><use :xlink:href="`#${item.icon}`"></use></svg>
                    </div>
                </div>
                <div class="notifications-item__content">
                    <div v-if="item.label" class="notifications-item__label">
                        {{ item.label }}
                    </div>
                    <div v-if="item.title" class="notifications-item__title">
                        {{ item.title }}
                    </div>
                    <div v-if="item.text" class="notifications-item__text">
                        {{ item.text }}
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup lang="ts">
import { useNotifications } from "@site/composables/useNotification";
</script>
