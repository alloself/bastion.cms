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
                <v-checkbox
                    v-if="'paginate' in pivot && item.depth === 0"
                    density="compact"
                    hide-details
                    @click.stop
                    class="mr-2"
                    label="Пагинация"
                    :model-value="Boolean(item.pivot.paginate)"
                    @update:model-value="
                        item.pivot.paginate = !item.pivot.paginate
                    "
                ></v-checkbox>
                <v-text-field
                    v-if="'key' in pivot && item.depth === 0"
                    density="compact"
                    hide-details
                    @click.stop
                    class="mr-2"
                    label="Ключ"
                    v-model="item.pivot.key"
                ></v-text-field>
                <order-buttons
                    v-if="ordered"
                    :item="item"
                    :morph="morph"
                    :module="module"
                    @update:order="onUpdateOrder"
                />
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

<script
    setup
    lang="ts"
    generic="T extends IBaseEntity & Maybe<IOrderedEntity> & INestedSetEntity<T>"
>
import { computed, Ref, ref } from "vue";
import { useModule } from "../composables";
import type {
    IBaseEntity,
    INestedSetEntity,
    IOrderedEntity,
    IRelationTreeProps,
    Maybe,
} from "../types";
import RelationCard from "./RelationCard.vue";
import { useItems } from "../composables/useItems";
import { useModalDrawerStore } from "../../features/modal-drawer";
import { useRelationMethods } from "../composables";
import OrderButtons from "./OrderButtons.vue";
import { orderBy } from "lodash";
const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
    itemValue,
    morph = false,
    ordered = false,
    pivot = {
        order: 0,
    },
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
        children: item?.children?.length
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

const processedItems = computed(() => {
    const items = processItems(modelValue)
    const isTree = items.some(({ parent_id }) => parent_id)
    const sortBy = isTree ? 'order' : 'pivot.order'
    return orderBy(items, [sortBy, "created_at"], ["desc"])
});

const { addRelation, editRelation, addExistingEntity, deleteSelected } =
    useRelationMethods<T>({
        module: module.value,
        initialValues,
    });

const onAddRelation = () => {
    addRelation((item: T) => {
        const data = item;
        if (morph) {
            data.pivot = pivot;
        }
        emit("update:model-value", [...modelValue, data]);
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

const onItemClick = (item: T) => {
    onEditRelation(item.id);
};

const onAddExistingEntity = async (items: T[]) => {
    if (morph) {
        emit("update:model-value", [
            ...modelValue,
            ...items.map((i) => {
                return {
                    ...i,
                    pivot,
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
                ...results.map((i) => {
                    return {
                        ...i,
                        pivot,
                    };
                }),
            ]);
        },
        loading
    );
};

const onUpdateOrder = (item: T) => {
    const updatedModel = [...modelValue];
    updateTreeItem(updatedModel, item);
};
</script>
