<template>
    <relation-card v-if="module" :module="module" :title="title" :getItemTitle="getItemTitle" :loading="loading"
        @event:create="onAddRelation" @event:delete="onDeleteSelected" @event:add-existing="onAddExistingEntity">
        <v-data-table :items="modelValue" :headers="headers" hide-default-footer hide-no-data show-select return-object
            v-model="selected">
            <template #[`item.pivot.value`]="{ item }">
                <v-text-field density="compact" hide-details v-model="item.pivot.value"></v-text-field>
            </template>
            <template #[`item.order`]="{ item }">
                <order-buttons v-if="ordered" :item="item" :morph="morph" :module="module" />
            </template>
            <template #[`item.pivot.key`]="{ item }">
                <v-text-field density="compact" hide-details v-model="item.pivot.key"></v-text-field>
            </template>
            <template #[`item.url`]="{ item }">
                <a :href="item.url" target="_blank">{{ item.url }}</a>
            </template>
            <template #[`item.preview`]="{ item }">
                <img :src="item.url" style="width: 150px; height: 150px; object-fit: contain" />
            </template>
            <template #[`item.actions`]="{ item }">
                <v-btn variant="text" size="small" icon="mdi-open-in-new" @click.stop="onEditRelation(item.id)">
                </v-btn>
            </template>
        </v-data-table>
    </relation-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & Maybe<IOrderedEntity>">
import RelationCard from "./RelationCard.vue";
import OrderButtons from "./OrderButtons.vue";
import { useItems, useModule } from "../composables";
import { IBaseEntity, IOrderedEntity, IRelationTableProps } from "../types";
import { useRelationMethods } from "../composables";
import { Ref, ref } from "vue";
import { Maybe } from "yup";

const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
    itemValue,
    morph = false,
    headers,
    ordered,
    title
} = defineProps<IRelationTableProps<T>>();
const emit = defineEmits<{ "update:model-value": [value: T[]] }>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });

const { addRelation, editRelation, addExistingEntity, deleteSelected } =
    useRelationMethods<T>({
        module: module.value,
        initialValues,
    });

const loading = ref(false);
const selected = ref([]) as Ref<T[]>;

const updateTreeItem = <T extends { id: string; children?: T[] }>(
    items: T[],
    updatedItem: T
): boolean => {
    for (const item of items) {
        if (item.id === updatedItem.id) {
            Object.assign(item, updatedItem);
            return true;
        }
    }
    return false;
};

const onAddRelation = () => {
    addRelation((item: T) => {
        if (morph) {
            item.pivot = {
                order: 0
            }
        }
        emit("update:model-value", [...modelValue, item]);
    });
};

const onAddExistingEntity = async (items: T[]) => {
    if (morph) {
        emit("update:model-value", [
            ...modelValue,
            ...items.map((el) => {
                return {
                    ...el,
                    pivot: {
                        value: "",
                    },
                };
            }),
        ]);
        return;
    }

    addExistingEntity(
        items,
        (results) => {
            emit("update:model-value", [
                ...modelValue,
                ...results.map((el) => {
                    return {
                        ...el,
                        pivot: {
                            value: "",
                        },
                    };
                }),
            ]);
        },
        loading
    );
};

const onDeleteSelected = async () => {
    try {
        if (!morph) {
            deleteSelected(selected.value);
        }
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

const onEditRelation = (id: string) => {
    editRelation(id, (item: T) => {
        const updatedModel = [...modelValue];
        updateTreeItem(updatedModel, item);
        emit("update:model-value", updatedModel);
    });
};
</script>
