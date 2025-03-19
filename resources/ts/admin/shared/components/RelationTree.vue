<template>
    <relation-card :title="module?.title" :icon="module?.icon">
        <template #title:append
            ><v-btn
                :icon="expandAll ? 'mdi-collapse-all' : 'mdi-expand-all'"
                variant="text"
                @click="expandAll = !expandAll"
            ></v-btn
        ></template>
        <v-text-field
            v-model="search"
            density="compact"
            placeholder="Поиск"
            prepend-inner-icon="mdi-magnify"
            clearable
            hide-details
            @click:clear="search = ''"
        />

        <v-treeview
            v-model:opened="opened"
            v-model:selected="selected"
            :items="processedItems"
            :search="search"
            :open-all="expandAll"
            :item-value="getItemValue"
            :item-title="getItemTitle"
            open-on-click
        >
            <template #prepend="{ item, isSelected, select }">
                <v-checkbox
                    v-if="item.depth === 0"
                    density="compact"
                    :model-value="isSelected"
                    @click.stop="select"
                    hide-details
                    class="mr-2"
                />
            </template>
            <template #append="{ item }">
                <div
                    class="d-flex items-center ga-2"
                    @click.stop
                    v-if="ordered"
                >
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
                <v-btn
                    variant="text"
                    size="small"
                    icon="mdi-open-in-new"
                    @click.stop="onItemClick(item)"
                >
                </v-btn>
            </template>
        </v-treeview>

        <template #actions>
            <v-tooltip location="top" text="Создать" color="primary">
                <template #activator="{ props }">
                    <v-btn icon large v-bind="props" @click="addRelation" flat>
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
                                    v-model="searchedModelValue"
                                    :item-title="getItemTitle"
                                    multiple
                                    return-object
                                    chips
                                    closable-chips
                                    v-model:search="search"
                                    :items="searchedItems"
                                ></v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-card-text>

                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn
                            variant="text"
                            @click="
                                (showSearch = false),
                                    ((searchedModelValue = []), (search = ''))
                            "
                        >
                            Отмена
                        </v-btn>
                        <v-btn
                            color="primary"
                            variant="text"
                            @click="addExistingEntity"
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

<script
    setup
    lang="ts"
    generic="T extends IBaseEntity & INestedSetEntity<T> & Record<string, any>"
>
import { computed, Ref, ref, watch } from "vue";
import { useModule } from "../composables";
import type {
    IBaseEntity,
    INestedSetEntity,
    IRelationTreeProps,
} from "../types";
import RelationCard from "./RelationCard.vue";
import { useItems } from "../composables/useItems";
import { useModalDrawerStore } from "../../features/modal-drawer";
import { client } from "../api/axios";
import { getModuleUrlPart } from "../modules";
import { debounce } from "lodash";

const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
    itemValue,
    morph = false,
    ordered = false,
} = defineProps<IRelationTreeProps<T>>();
const emit = defineEmits<{ "update:model-value": [value: T[]] }>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });
const modalDrawerStore = useModalDrawerStore();

const selected = ref<T[]>([]) as Ref<T[]>;
const opened = ref<T[]>([]);
const search = ref("");
const expandAll = ref(true);
const loading = ref(false);
const showSearch = ref(false);
const searchedModelValue = ref();
const searchedItems = ref();

const processItems = (items: T[], depth = 0): T[] => {
    return items.map((item) => ({
        ...item,
        depth,
        children: item.children.length
            ? processItems(item.children, depth + 1)
            : undefined,
    }));
};

const updateTreeItem = <T extends { id: string; children?: T[] }>(
    items: T[],
    updatedItem: T
): boolean => {
    for (const item of items) {
        if (item.id === updatedItem.id) {
            Object.assign(item, updatedItem);
            return true;
        }
        if (item.children?.length) {
            const found = updateTreeItem(item.children, updatedItem);
            if (found) return true;
        }
    }
    return false;
};

const processedItems = computed(() => processItems(modelValue));

const addRelation = () => {
    modalDrawerStore.addDetailModal(
        {
            module: module.value,
            initialValues: initialValues ? { ...initialValues } : undefined,
        },
        {
            onCreate: (item: T) => {
                emit("update:model-value", [...modelValue, item]);
                modalDrawerStore.onModalClose();
            },
        }
    );
};

const editRelation = (id: string) => {
    modalDrawerStore.addDetailModal(
        { module: module.value, id },
        {
            onUpdate: (updatedItem: T) => {
                if (!updatedItem) {
                    return;
                }
                const updatedModel = [...modelValue];
                updateTreeItem(updatedModel, updatedItem);
                emit("update:model-value", updatedModel);
            },
        }
    );
};

const deleteSelected = async () => {
    try {
        await Promise.all(
            selected.value.map((item) =>
                client.patch(
                    `/api/admin/${getModuleUrlPart(moduleKey)}/${item.id}`,
                    {
                        ...item,
                        parent_id: null,
                    }
                )
            )
        );

        const selectedIds = new Set(selected.value.map(getItemValue));
        emit(
            "update:model-value",
            modelValue.filter((item) => !selectedIds.has(getItemValue(item)))
        );
        selected.value = [];
    } catch (e) {
        console.error("Deletion failed:", e);
    }
};

const onItemClick = (item: T) => {
    editRelation(item.id);
};

const getSearchedItems = async (string = "") => {
    try {
        loading.value = true;
        const { data } = await client.get(
            `/api/admin/${getModuleUrlPart(moduleKey)}`,
            {
                params: {
                    search: string,
                    with: module.value?.relations,
                },
            }
        );
        searchedItems.value = data;
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false;
    }
};

const addExistingEntity = async () => {
    if (morph) {
        emit("update:model-value", [
            ...modelValue,
            ...searchedModelValue.value,
        ]);
        searchedModelValue.value = [];
        showSearch.value = false;
        return;
    }

    try {
        loading.value = true;
        const results = await Promise.all(
            searchedModelValue.value.map(async (item: T) => {
                const { data } = await client.patch(
                    `/api/admin/${getModuleUrlPart(moduleKey)}/${item.id}`,
                    { ...item, ...initialValues }
                );
                return data;
            })
        );

        emit("update:model-value", [...modelValue, ...results]);
        searchedModelValue.value = [];
    } finally {
        loading.value = false;
        showSearch.value = false;
    }
};

const updateOrder = async (item: T, value: string | number) => {
    if (morph && item.depth === 0) {
        item.pivot.order = Number(value);
    } else {
        const { data } = await client.patch(
            `/api/admin/${getModuleUrlPart(moduleKey)}/${item.id}`,
            {
                ...item,
                order: value,
            }
        );
        const updatedModel = [...modelValue];
        updateTreeItem(updatedModel, data);
    }
};

const getItmOrder = (item: T) => {
    if (morph && item.depth === 0) {
        return item.pivot.order;
    }
    return item.order;
};

watch(
    search,
    debounce((newValue) => {
        if (!newValue) return;
        getSearchedItems(newValue);
    }, 300)
);
</script>
