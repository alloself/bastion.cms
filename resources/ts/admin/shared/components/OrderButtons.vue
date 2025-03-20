<template>
    <div class="d-flex items-center ga-2" @click.stop v-if="ordered">
        <v-btn
            icon="mdi-arrow-up-bold-circle-outline"
            size="small"
            @click="updateOrder(item, getItmOrder(item) + 1)"
            variant="text"
        >
        </v-btn>
        <v-text-field
            class="centered-input"
            hide-details="auto"
            :model-value="getItmOrder(item)"
            @update:model-value="(v) => updateOrder(item, v)"
            density="compact"
        >
        </v-text-field>
        <v-btn
            icon="mdi-arrow-down-bold-circle-outline"
            size="small"
            variant="text"
            @click="updateOrder(item, getItmOrder(item) - 1)"
        >
        </v-btn>
    </div>
</template>

<script setup lang="ts">
</script>
