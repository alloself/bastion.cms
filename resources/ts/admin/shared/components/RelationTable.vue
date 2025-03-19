<template>
    <relation-card :title="title" :icon="icon" :loading="loading">
        <template #default>
            <v-data-table-server
                v-model="selected"
                :headers="headers"
                :items-length="items.total || 0"
                :items="items.data"
                :loading="loading"
                v-model:page="tableProps.page"
                v-model:items-per-page="tableProps.itemsPerPage"
                v-model:sort-by="tableProps.sortBy"
                show-select
                fixed-header
                hide-no-data
            >
                <template #top>
                    <v-text-field
                        prepend-inner-icon="mdi-magnify"
                        variant="solo"
                        density="compact"
                        label="Поиск"
                        class="ml-1 mt-2 search"
                        v-model="tableProps.search"
                        clearable
                        hide-details
                        @click:clear="setSearch"
                        @keypress.enter="setSearch"
                        single-line
                    ></v-text-field>
                </template>

                <template #bottom>
                    <div class="d-flex align-center justify-end w-100 px-2">
                        <v-select
                            density="compact"
                            class="per-page-counter"
                            v-model="tableProps.itemsPerPage"
                            variant="solo"
                            hide-details
                            :items="[5, 10, 15, 25, 50, 100]"
                        ></v-select>
                        <v-pagination
                            density="compact"
                            rounded="circle"
                            :show-first-last-page="true"
                            v-model="tableProps.page"
                            :length="items.last_page"
                            :total-visible="6"
                        ></v-pagination>
                    </div>
                </template>
            </v-data-table-server>
        </template>

        <template #actions>
            <v-tooltip location="top" text="Создать" color="primary">
                <template #activator="{ props }">
                    <v-btn icon large v-bind="props" @click="addRelation" flat>
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
            </v-tooltip>

            <v-tooltip location="top" text="Удалить выбранное" color="primary">
                <template #activator="{ props }">
                    <v-btn
                        icon
                        large
                        :disabled="!selected.length"
                        v-bind="props"
                        flat
                        @click="deleteSelected"
                    >
                        <v-icon>mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-tooltip>
        </template>
    </relation-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import { ref, watch } from "vue";
import type { IBaseEntity, IServerDataList, ITableProps } from "@admin/shared/types";
import RelationCard from "./RelationCard.vue";
import { client } from "@admin/shared/api/axios";
import { getModuleUrlPart } from "../modules";
import { useModalDrawerStore } from "../../features/modal-drawer";

interface IRelationTableProps<T> {
    moduleKey: string;
    title?: string;
    icon?: string;
    headers: { title: string; key: string }[];
    initialValues?: Partial<T>;
}

const { moduleKey, title, icon, headers, initialValues } = defineProps<IRelationTableProps<T>>();
const emit = defineEmits<{ "update:modelValue": [value: T[]] }>();

const modalDrawerStore = useModalDrawerStore();
const loading = ref(false);
const selected = ref<T[]>([]);

const items = ref<IServerDataList<T>>({
    total: 0,
    data: [],
    current_page: 0,
    last_page: 0,
    per_page: 0,
});

const tableProps = ref<ITableProps>({
    page: 1,
    itemsPerPage: 15,
    sortBy: [],
    search: "",
});

const setSearch = () => {
    tableProps.value.page = 1;
    getItems(tableProps.value);
};

const getItems = async (options: Record<string, unknown>) => {
    try {
        loading.value = true;
        const { data } = await client.get(`/api/admin/${getModuleUrlPart(moduleKey)}`, {
            params: options,
        });
        items.value = data;
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const addRelation = () => {
    modalDrawerStore.addDetailModal(
        {
            module: {
                key: moduleKey,
            },
            initialValues,
        },
        {
            onCreate: (item: T) => {
                getItems(tableProps.value);
                modalDrawerStore.onModalClose();
            },
        }
    );
};

const deleteSelected = async () => {
    try {
        loading.value = true;
        await client.post(`/api/admin/destroy/${getModuleUrlPart(moduleKey)}`, {
            ids: selected.value.map((item) => item.id),
        });
        selected.value = [];
        getItems(tableProps.value);
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

watch(
    tableProps,
    async (value) => {
        await getItems(value);
    },
    {
        deep: true,
        immediate: true,
    }
);
</script>

<style lang="scss">
.per-page-counter {
    max-width: 90px;
    width: 90px;

    .v-field {
        box-shadow: none;
    }
}

.search {
    .v-field {
        box-shadow: none;
    }
}
</style> 