<template>
    <v-sheet class="mx-auto w-75" style="background-color: transparent;">
        <v-card class="mb-4" prepend-icon="mdi-file-import-outline">
            <template #title>
                <span class="font-weight-black">Импорт каталога</span>
            </template>

            <v-card-text class="bg-surface-light pt-4">
                <v-file-input label="Файл импорта" v-model="importFile"></v-file-input>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="sendFile">Загрузить</v-btn>
            </v-card-actions>
        </v-card>

        <v-card  prepend-icon="mdi-file-import-outline">
            <template #title>
                <span class="font-weight-black">Импорт цен</span>
            </template>

            <v-card-text class="bg-surface-light pt-4">
                <v-file-input label="Файл импорта" v-model="importPriceFile"></v-file-input>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="sendPriceFile">Загрузить</v-btn>
            </v-card-actions>
        </v-card>
    </v-sheet>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { client } from "../shared/api/axios";

const importFile = ref();
const importPriceFile = ref();

const sendFile = async () => {
    try {
        await client.post("/api/admin/import", {
            file: importFile.value,
        });
    } catch (error) {
        console.error("Ошибка загрузки", error);
    }
};

const sendPriceFile = async () => {
    try {
        await client.post("/api/admin/import/price", {
            file: importPriceFile.value,
        });
    } catch (error) {
        console.error("Ошибка загрузки", error);
    }
};
</script>
