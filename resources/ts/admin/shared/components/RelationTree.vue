<template>
    <relation-card :title="module?.title" :icon="module?.icon">
        <tree-view
            v-model:selected="selected"
            :items="modelValue"
            class="flex-1-0"
            :item-title="getItemTitle"
            :item-value="getItemValue"
            select-strategy="classic"
            @click:select="onItemClick"
            selectable
            return-object
        ></tree-view>
        <template #actions>
            <v-tooltip location="top" text="Создать" color="primary">
                <template #activator="{ props }">
                    <v-btn icon large v-bind="props" @click="addRelation" flat>
                        <v-icon>mdi-plus</v-icon>
                    </v-btn>
                </template>
                <span>Создать</span>
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
                                    ((searchedModelValue = {}), (search = ''))
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
                <span>Удалить выбранное</span>
            </v-tooltip>
        </template>
    </relation-card>
</template>

<script
    setup
    lang="ts"
    generic="T extends IBaseEntity & INestedSet<T>"
>
import { ref, watch } from "vue";
import { useModule } from "../composables";
import type { IBaseEntity, INestedSet, IRelationTreeProps } from "../types";
import RelationCard from "./RelationCard.vue";
import { useItems } from "../composables/useItems";
import { useModalDrawerStore } from "../../features/modal-drawer";
import { client } from "../api/axios";
import TreeView from "./TreeView.vue";

const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
    itemValue,
} = defineProps<IRelationTreeProps<T>>();

const emits = defineEmits<{
    "update:model-value": [value: T[]];
}>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });
const modalDrawerStore = useModalDrawerStore();

const selected = ref([]);
const showSearch = ref(false);
const searchedModelValue = ref();
const searchedItems = ref([]);
const search = ref("");

const addRelation = () => {
    modalDrawerStore.addDetailModal(
        {
            module: module.value,
        },
        {
            onCreate: (item: T) => {},
        }
    );
};

const editRelation = (id: string) => {
    modalDrawerStore.addDetailModal(
        {
            module: module.value,
            id,
        },
        {
            onUpdate: (updatedItem: T) => {},
        }
    );
};

const addExistingEntity = () => {
    searchedModelValue.value.forEach(async (item: T) => {
        const { data } = await client.patch(
            `/api/admin/${moduleKey}/${item.id}`,
            {
                ...item,
                ...initialValues,
            }
        );
        emits("update:model-value", [...modelValue, data]);
    });

    showSearch.value = false;
};

const deleteSelected = () => {};

const onItemClick = ({ id }: { id: unknown }) => {
  const item = id as T
  editRelation(item.id)
};

const getSearchedItems = async (string = "") => {
    try {
        const { data } = await client.get(`/api/admin/${moduleKey}`, {
            params: {
                search: string,
                with: module.value?.relations,
            },
        });
        searchedItems.value = data;
    } catch (e) {
        console.log(e);
    }
};

watch(search, (newValue) => {
    if (!newValue) {
        return;
    }
    getSearchedItems(newValue);
});
</script>
