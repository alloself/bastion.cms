<template>
    <relation-card :title="module?.title" :icon="module?.icon">
        <v-text-field
            v-model="search"
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
            <template v-slot:append="{ item }">
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
import { computed, Ref, ref } from "vue";
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

const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
    itemValue,
    morph = false,
} = defineProps<IRelationTreeProps<T>>();
const emit = defineEmits<{ "update:model-value": [value: T[]] }>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });
const modalDrawerStore = useModalDrawerStore();

const selected = ref<T[]>([]) as Ref<T[]>;
const opened = ref<T[]>([]);
const search = ref("");

const processItems = (items: T[], depth = 0): T[] => {
    return items.map((item) => ({
        ...item,
        depth,
        children: item.children.length
            ? processItems(item.children, depth + 1)
            : undefined,
    }));
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
                const updateTree = (items: T[]): T[] =>
                    items.map((item) =>
                        item.id === updatedItem.id
                            ? updatedItem
                            : {
                                  ...item,
                                  children: item.children
                                      ? updateTree(item.children)
                                      : [],
                              }
                    );

                emit("update:model-value", updateTree(modelValue));
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
</script>
