<template>
    <v-card
        variant="tonal"
        flat
        :loading="loading"
        :prepend-icon="module ? module.icon : icon"
        class="mb-8"
    >
        <template #title>
            <div class="d-flex align-center">
                <span class="ml-2">
                    {{ title || module?.title }}
                </span>
                <v-spacer></v-spacer>
                <slot name="title:append"></slot>
            </div>
        </template>
        <slot name="default"></slot>
        <v-divider></v-divider>
        <v-card-actions>
            <slot name="actions">
                <v-tooltip location="top" text="Создать" color="primary">
                    <template #activator="{ props }">
                        <v-btn icon large v-bind="props" @click="onCreate" flat>
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </template>
                </v-tooltip>

                <v-menu
                    v-model="showSearch"
                    :close-on-content-click="false"
                    location="right"
                    offset="16"
                >
                    <template v-slot:activator="menu">
                        <v-tooltip location="top" text="Поиск" color="primary">
                            <template #activator="tooltip">
                                <v-btn
                                    :loading="loading"
                                    icon
                                    large
                                    v-bind="{ ...tooltip.props, ...menu.props }"
                                    flat
                                    @click="showSearch = true"
                                >
                                    <v-icon>mdi-magnify</v-icon>
                                </v-btn>
                            </template>
                            <span>Поиск</span>
                        </v-tooltip>
                    </template>

                    <v-card width="500">
                        <v-card-title>Найти</v-card-title>
                        <v-card-text class="mt-2">
                            <v-row>
                                <v-col>
                                    <v-autocomplete
                                        placeholder="Поиск"
                                        v-model="searchingModelValue"
                                        :item-title="getItemTitle"
                                        multiple
                                        return-object
                                        chips
                                        closable-chips
                                        v-model:search="search"
                                        :items="searchingItems"
                                    ></v-autocomplete>
                                </v-col>
                            </v-row>
                        </v-card-text>

                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-spacer></v-spacer>

                            <v-btn variant="text" @click="onCancelSearch">
                                Отмена
                            </v-btn>
                            <v-btn
                                color="primary"
                                variant="text"
                                @click="onAddExistingEntity"
                            >
                                Добавить
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-menu>

                <v-tooltip
                    location="top"
                    text="Удалить выбранное"
                    color="primary"
                >
                    <template #activator="{ props }">
                        <v-btn icon large v-bind="props" flat @click="onDelete">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                </v-tooltip>
            </slot>
        </v-card-actions>
    </v-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import { IBaseEntity, type IRelationCardProps } from "@admin/shared/types";
import { debounce } from "lodash";
import { ref, watch } from "vue";
import { client } from "../api/axios";
import { getModuleUrlPart } from "../modules";

const { module, getItemTitle, title, icon } =
    defineProps<IRelationCardProps<T>>();

const emit = defineEmits<{
    "event:create": [];
    "event:add-existing": [value: T[]];
    "event:delete": [];
}>();

const showSearch = ref(false);
const search = ref("");
const searchingModelValue = ref([]);
const searchingItems = ref([]);
const loading = ref(false);

const onCreate = () => {
    emit("event:create");
};

const onCancelSearch = () => {
    showSearch.value = false;
    searchingModelValue.value = [];
    search.value = "";
};

const onAddExistingEntity = () => {
    emit("event:add-existing", searchingModelValue.value);
    searchingModelValue.value = [];
    showSearch.value = false;
};

const onDelete = () => {
    emit("event:delete");
};

const getSearchedItems = async (string = "") => {
    if (!module) {
        return;
    }
    try {
        loading.value = true;
        const { data } = await client.get(
            `/api/admin/${getModuleUrlPart(module?.url || module.key)}`,
            {
                params: {
                    search: string,
                    with: module?.relations,
                },
            }
        );
        searchingItems.value = data;
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false;
    }
};

watch(
    search,
    debounce((newValue) => {
        if (!newValue) return;
        getSearchedItems(newValue);
    }, 300)
);

watch(showSearch, () => {
    search.value = "";
    searchingItems.value = [];
});
</script>
