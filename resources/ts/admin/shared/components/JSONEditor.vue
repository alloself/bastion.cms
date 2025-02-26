<template>
    <relation-card :title="title" :icon="icon">
        <template #default>
            <v-data-table
                v-model="selected"
                show-select
                return-object
                :headers="headers"
                :items="items"
                :loading="loading"
                :items-per-page="1000"
            >
                <template #bottom>
                    <div></div>
                </template>
                <template #[`item.key`]="{ item }">
                    <span
                        contenteditable="true"
                        class="px-2 py-2 w-100 d-flex"
                        @input="(v) => onInput(v, item, 'key')"
                        >{{ item.key }}</span
                    >
                </template>
                <template #[`item.value`]="{ item }">
                    <span
                        contenteditable="true"
                        class="px-2 py-2 w-100 d-flex"
                        @input="(v) => onInput(v, item, 'value')"
                        >{{ item.value }}</span
                    >
                </template>
            </v-data-table>
        </template>
        <template #actions>
            <v-menu
                v-model="showCreate"
                :close-on-content-click="false"
                location="right"
                offset="16"
            >
                <template v-slot:activator="menu">
                    <v-tooltip location="top" text="Создать" color="primary">
                        <template #activator="tooltip">
                            <v-btn
                                icon
                                large
                                v-bind="{ ...tooltip.props, ...menu.props }"
                                :loading="loading"
                                flat
                                @click="showCreate = true"
                            >
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </template>
                        <span>Создать</span>
                    </v-tooltip>
                </template>

                <v-card width="500">
                    <v-card-title>Добавить</v-card-title>
                    <v-card-text class="mt-2">
                        <v-row>
                            <v-col cols="6">
                                <v-text-field
                                    label="Ключ"
                                    v-model="newProperty.key"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    label="Значение"
                                    v-model="newProperty.value"
                                ></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn variant="text" @click="showCreate = false">
                            Отмена
                        </v-btn>
                        <v-btn
                            color="primary"
                            variant="text"
                            @click="addNewProperty"
                            :disabled="!newProperty.key || !newProperty.value"
                        >
                            Добавить
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-menu>

            <v-tooltip location="top" text="Удалить выбранное" color="primary">
                <template #activator="{ props }">
                    <v-btn
                        icon
                        large
                        :loading="loading"
                        v-bind="props"
                        flat
                        @click="removeSelected"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
                <span>Удалить выбранное</span>
            </v-tooltip>
            <v-spacer></v-spacer>
        </template>
    </relation-card>
</template>

<script setup lang="ts">
import { differenceBy } from "lodash";
import { ref, watch } from "vue";
import { type IJSONEditorProps } from "@admin/shared/types";
import RelationCard from "./RelationCard.vue";

const {
    loading,
    title,
    icon = "mdi-code-json",
    modelValue = {},
} = defineProps<IJSONEditorProps>();

const emits = defineEmits(["update:model-value"]);

const selected = ref<any>([]);
const newProperty = ref({
    key: "",
    value: "",
});
const showCreate = ref(false);

const headers = ref([
    {
        title: "Ключ",
        key: "key",
    },
    {
        title: "Значение",
        key: "value",
    },
]);
const items = ref<{ key: string; value: string }[]>([]);

const addNewProperty = () => {
    emits("update:model-value", {
        ...modelValue,
        [newProperty.value.key]: newProperty.value.value,
    });
    newProperty.value = {
        key: "",
        value: "",
    };
    showCreate.value = false;
};

const removeSelected = () => {
    const newValue = differenceBy(items.value, selected.value, "key");

    emits(
        "update:model-value",
        newValue.reduce((acc, item) => {
            acc[item.key] = item.value;
            return acc;
        }, {} as Record<string, string>)
    );
};

const onInput = (e: Event, item: Record<string, string>, key: string) => {
    const target = e.target as HTMLElement;
    item[key] = target.outerText;

    emits(
        "update:model-value",
        items.value.reduce((acc, item) => {
            acc[item.key] = item.value;
            return acc;
        }, {} as Record<string, string>)
    );
};

watch(
    () => modelValue,
    (value) => {
        if (!value) {
            return;
        }

        items.value = Object.keys(value).map((key) => {
            return {
                key: key,
                value: modelValue[key],
            };
        });
    },
    {
        deep: true,
        immediate: true,
    }
);
</script>
