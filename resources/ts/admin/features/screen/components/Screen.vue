<template>
    <v-card outlined rounded="0" flat class="h-100 d-flex flex-column overflow-auto w-100">
        <v-card-title class="d-flex align-center" :class="{ 'ga-2': screens.length }">
            <div class="d-flex align-center ga-2">
                <v-chip size="small" label v-for="screen in screens" closable> {{screen}} </v-chip>
            </div>
            <v-btn size="x-small" icon="mdi-plus" variant="flat" />

            <v-spacer></v-spacer>
            <v-btn size="x-small" icon="mdi-dock-window" variant="flat" />
            <v-btn size="x-small" icon="mdi-close" variant="flat" />
        </v-card-title>
        <v-card-text class="bg-surface-light">
            <slot name="default">
                <CodeEditor v-model="test"/>
            </slot>
        </v-card-text>
        <v-card-actions v-if="slots.actions">
            <slot name="actions"></slot>
        </v-card-actions>
    </v-card>
    <v-divider vertical class="divider"></v-divider>
</template>

<script setup lang="ts">
import  CodeEditor from "@/ts/admin/shared/components/CodeEditor.vue";
import { ref, useSlots } from "vue";

const screens = ref([1,23,4]);

const slots = useSlots();
const test = ref('')
</script>
<style lang="scss"></style>
