<template>
    <v-card flat class="pa-4" max-width="800">
        <v-skeleton-loader type="heading" class="mb-4" />

        <v-skeleton-loader
            v-for="i in fields"
            :key="i"
            type="text"
            class="mb-3"
        />
    </v-card>
</template>

<script setup lang="ts">
import { defineProps } from "vue";

const { fields = 8
 } = defineProps<{ fields?: number }>();
</script>

<style scoped>
.v-skeleton-loader {
    border-radius: 6px;
}
</style>
