<template>
    <v-autocomplete
        @click="onClick"
        :clearable="!readonly"
        :readonly="readonly"
        :loading="loading"
        v-model="modelValue"
        :itemValue="getItemValue"
        :item-title="getItemTitle"
        v-model:search="search"
        v-bind="$attrs"
        no-data-text="Нет данных"
        :items="items"
        :returnObject="returnObject"
    >
        <template #prepend>
            <v-tooltip location="top" color="primary">
                <template #activator="{ props }">
                    <v-btn
                        @click="addRelation"
                        v-bind="props"
                        size="small"
                        :disabled="readonly"
                        class="mr-2"
                        icon="mdi-plus"
                        aria-label="Создать новую запись"
                    ></v-btn>
                </template>
                <span>Создать</span>
            </v-tooltip>
            <v-tooltip location="top" color="primary">
                <template #activator="{ props }">
                    <v-btn
                        @click="editRelation"
                        v-bind="props"
                        size="small"
                        :disabled="!modelValue || readonly"
                        icon="mdi-pencil"
                        aria-label="Редактировать выбранную запись"
                    ></v-btn>
                </template>
                <span>Редактировать</span>
            </v-tooltip>
        </template>

        <template #prepend-inner>
            <v-icon :icon="module?.icon"></v-icon>
        </template>
    </v-autocomplete>
</template>

<script setup lang="ts" generic="T extends IBaseEntity">
import {
    type IBaseEntity,
    type IRelationAutocompleteProps,
} from "@admin/shared/types";
import { ref, watch, onUnmounted, type Ref } from "vue";
import { client } from "@admin/shared/api/axios";
import { debounce } from "lodash";
import { useModalDrawerStore } from "@admin/features/modal-drawer/store";
import { useModule } from "../composables/useModule";
import { useItems } from "../composables/useItems";
import { getModuleUrlPart } from "../modules";

const modalDrawerStore = useModalDrawerStore();

const {
    moduleKey,
    itemValue,
    itemTitle,
    readonly = false,
    initialItems = [],
    returnObject = false,
} = defineProps<IRelationAutocompleteProps<T>>();

const modelValue = defineModel<T>();

const emit = defineEmits<{
    "update:model-value": [value: T[keyof T] | string | T];
}>();

const items = ref<T[]>(initialItems) as Ref<T[]>;
const search = ref("");
const loading = ref(false);

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });

const getItems = async (options: Record<string, string> = {}) => {
    loading.value = true;
    try {
        const { data } = await client.get(
            `/api/admin/${getModuleUrlPart(moduleKey)}`,
            {
                params: {
                    ...options,
                    with: module.value?.relations,
                },
            }
        );

        items.value = data;
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false;
    }
};

const handleSearch = debounce(getItems, 300);

const addRelation = () => {
    modalDrawerStore.addDetailModal(
        {
            module: module.value,
        },
        {
            onCreate: (item: T) => {
                search.value = "";
                items.value.push(item);
                if (returnObject) {
                    emit("update:model-value", item);
                } else {
                    const value = getItemValue(item);
                    if (value) {
                        emit("update:model-value", value);
                    }
                }

                modalDrawerStore.onModalClose();
            },
        }
    );
};

const editRelation = () => {
    modalDrawerStore.addDetailModal(
        {
            module: module.value,
            id: returnObject ? modelValue.value?.id : modelValue.value,
        },
        {
            onUpdate: (updatedItem: T) => {
                const index = items.value.findIndex(
                    (item: T) =>
                        getItemValue(item) === getItemValue(updatedItem)
                );
                if (index > -1) {
                    items.value.splice(index, 1, updatedItem);
                }
            },
        }
    );
};

const onClick = (e: Event) => {
    if (readonly) {
        e.preventDefault();
        e.stopImmediatePropagation();
    }
};

watch(
    [search, modelValue],
    ([newSearchVal, newmodelValue]) => {
        if (!newmodelValue && !newSearchVal) {
            return;
        }
        if (!newSearchVal) {
            handleSearch();
        } else {
            handleSearch({ search: newSearchVal });
        }
    },
);

watch(
    () => initialItems,
    (newVal) => {
        items.value = [...newVal];
    },
    { immediate: true, deep: true }
);

onUnmounted(() => {
    handleSearch.cancel();
});
</script>
