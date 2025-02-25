<template>
    <v-card
        variant="tonal"
        flat
        :loading="loading"
        :prepend-icon="icon"
        class="mb-8"
    >
        <template #title>
            <span class="ml-2">
                {{ title }}
            </span>
        </template>
        <slot name="default"></slot>
        <v-divider></v-divider>
        <v-card-actions>
            <slot name="actions"></slot>
        </v-card-actions>
    </v-card>
</template>

<script setup lang="ts">
import { type IRelationCardProps } from "@admin/shared/types";

const { loading, title, icon } = defineProps<IRelationCardProps>();
</script>
