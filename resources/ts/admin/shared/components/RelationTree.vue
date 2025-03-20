<template>
    <relation-card
        v-if="module"
        :module="module"
        :get-item-title="getItemTitle"
        @event:create="onAddRelation"
        @event:delete="onDeleteSelected"
        @event:add-existing="onAddExistingEntity"
    >
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
    </relation-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSetEntity<T>">
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
import { useRelationMethods } from "../composables/useRelationMethods";

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

const { addRelation, editRelation, addExistingEntity,deleteSelected } = useRelationMethods<T>({
    module: module.value,
    initialValues,
});

const onAddRelation = () => {
    addRelation((item: T) => {
        emit("update:model-value", [...modelValue, item]);
        modalDrawerStore.onModalClose();
    });
};

const onEditRelation = (id: string) => {
    editRelation(id, (item: T) => {
        const updatedModel = [...modelValue];
        updateTreeItem(updatedModel, item);
        emit("update:model-value", updatedModel);
    });
};

const onDeleteSelected = async () => {
    try {
        if (!morph) {
          deleteSelected(selected.value)
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

const onItemClick = (item: T) => {
    onEditRelation(item.id);
};

const onAddExistingEntity = async (items: T[]) => {
    if (morph) {
        emit("update:model-value", [...modelValue, ...items]);
        return;
    }

    addExistingEntity(
        items,
        (results) => {
            emit("update:model-value", [...modelValue, ...results]);
        },
        loading
    );
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
</script>
