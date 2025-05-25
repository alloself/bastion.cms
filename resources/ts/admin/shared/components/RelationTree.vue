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
                @click="toggleExpandAll"
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
            :items="processedItems"
            :search="search"
            :open-on-click="false"
            :open-all="expandAll"
            :item-value="getItemValue"
            :item-title="getItemTitle"
            v-model:active="active"
        >
            <template #prepend="{ item }">
                <v-checkbox
                    v-if="item.depth === 0"
                    density="compact"
                    :model-value="selected.includes(item.id)"
                    @click.stop="toggleSelect(item)"
                    hide-details
                    class="mr-2"
                />
            </template>
            <template #title="{ item }">
                <div>{{ getItemTitle(item) }}</div>
            </template>
            <template #append="{ item }">
                <v-checkbox
                    v-if="
                        item.pivot &&
                        'paginate' in item.pivot &&
                        item.depth === 0
                    "
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
                    v-if="item.pivot && 'key' in item.pivot && item.depth === 0"
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
import { computed, ref } from "vue";
import { useModule } from "../composables/useModule";
import type {
    IBaseEntity,
    INestedSetEntity,
    IOrderedEntity,
    IRelationTreeProps,
    Maybe,
} from "../types";
import RelationCard from "./RelationCard.vue";
import { useItems } from "../composables/useItems";
import { useRelationMethods } from "../composables/useRelationMethods";
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
    initialItems = {},
} = defineProps<IRelationTreeProps<T>>();
const emit = defineEmits<{ "update:model-value": [value: T[]] }>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });

const selected = ref<string[]>([]);
const active = ref<string[]>([]);
const search = ref("");
const expandAll = ref(false);
const loading = ref(false);

const toggleExpandAll = () => {
    expandAll.value = !expandAll.value;
};

const processItems = (items: T[], depth = 0): T[] => {
    return items.map((item) => ({
        ...item,
        title: getItemTitle(item),
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
    const items = processItems(modelValue);
    const isTree = items.some(({ parent_id }) => parent_id);
    const sortBy = isTree ? "order" : "pivot.order";
    return orderBy(items, [sortBy, "created_at"], ["desc"]);
});

const toggleSelect = (item: T) => {
    const index = selected.value.indexOf(item.id);
    if (index === -1) {
        selected.value.push(item.id);
    } else {
        selected.value.splice(index, 1);
    }
};

const { addRelation, editRelation, addExistingEntity, deleteSelected } =
    useRelationMethods<T>({
        module: module.value,
        initialValues,
        initialItems,
    });

const onAddRelation = () => {
    addRelation((item: T) => {
        const data = item;
        if (morph) {
            data.pivot = pivot || {};
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
            const selectedItems = modelValue.filter((item) =>
                selected.value.includes(item.id)
            );
            deleteSelected(selectedItems);
        }
        emit(
            "update:model-value",
            modelValue.filter((item) => !selected.value.includes(item.id))
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
                    pivot: pivot || {},
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
