<template>
  <relation-card :title="module?.title" :icon="module?.icon">
    <tree-view v-model:selected="selected" :items="modelValue" class="flex-1-0" :item-title="getItemTitle"
      :item-value="getItemValue" select-strategy="classic" @item:click="onItemClick" selectable
      return-object></tree-view>
    <template #actions>
      <v-tooltip location="top" text="Создать" color="primary">
        <template #activator="{ props }">
          <v-btn :loading="loading" icon large v-bind="props" @click="addRelation" flat>
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </template>
        <span>Создать</span>
      </v-tooltip>
      <v-menu v-model="showSearch" :close-on-content-click="false" location="right" offset="16">
        <template v-slot:activator="menu">
          <v-tooltip location="top" text="Поиск" color="primary">
            <template #activator="tooltip">
              <v-btn :loading="loading" icon large v-bind="{ ...tooltip.props, ...menu.props }" flat
                @click="showSearch = true">
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
                <v-autocomplete placeholder="Поиск" v-model="searchedModelValue" :item-title="getItemTitle" multiple
                  return-object chips closable-chips v-model:search="search" :items="searchedItems"></v-autocomplete>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>
          <v-card-actions>
            <v-spacer></v-spacer>

            <v-btn variant="text" @click="
              (showSearch = false),
              ((searchedModelValue = []), (search = ''))
              ">
              Отмена
            </v-btn>
            <v-btn color="primary" variant="text" @click="addExistingEntity">
              Добавить
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-menu>
      <v-tooltip location="top" text="Удалить выбранное" color="primary">
        <template #activator="{ props }">
          <v-btn :loading="loading" icon large :disabled="!selected.length" v-bind="props" flat @click="deleteSelected">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
        <span>Удалить выбранное</span>
      </v-tooltip>
    </template>
  </relation-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSetEntity<T>">
import { Ref, ref, watch } from "vue";
import { useModule } from "../composables";
import type { IBaseEntity, INestedSetEntity, IRelationTreeProps } from "../types";
import RelationCard from "./RelationCard.vue";
import { useItems } from "../composables/useItems";
import { useModalDrawerStore } from "../../features/modal-drawer";
import { client } from "../api/axios";
import { TreeView } from "./TreeView";
import { debounce } from "lodash";
import { getModuleUrlPart } from "../modules";

const {
  moduleKey,
  modelValue = [],
  initialValues,
  itemTitle,
  itemValue,
  morph = false
} = defineProps<IRelationTreeProps<T>>();

const emit = defineEmits<{
  "update:model-value": [value: T[]];
}>();

const { module } = useModule(moduleKey);
const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });
const modalDrawerStore = useModalDrawerStore();

const selected = ref([]) as Ref<T[]>;
const showSearch = ref(false);
const searchedModelValue = ref();
const searchedItems = ref();
const search = ref("");
const loading = ref(false);

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

const addRelation = () => {
  modalDrawerStore.addDetailModal(
    {
      module: module.value,
      initialValues: initialValues ? { ...initialValues } : undefined,
    },
    {
      onCreate: (item: T) => {
        emit("update:model-value", [...modelValue, item]);
        modalDrawerStore.onModalClose()
      },
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

const addExistingEntity = async () => {
  if(morph) {
    emit("update:model-value", [...modelValue, ... searchedModelValue.value]);
    searchedModelValue.value = [];
    showSearch.value = false;
    return;
  }

  try {
    loading.value = true
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
    loading.value = false
    showSearch.value = false;
  }
};

const deleteSelected = async () => {
  try {
    await Promise.all(
      selected.value.map(item =>
        client.patch(`/api/admin/${getModuleUrlPart(moduleKey)}/${item.id}`, {
          ...item,
          parent_id: null
        })
      )
    );

    const selectedIds = new Set(selected.value.map(getItemValue));
    emit('update:model-value',
      modelValue.filter(item => !selectedIds.has(getItemValue(item)))
    );
    selected.value = [];
  } catch (e) {
    console.error('Deletion failed:', e);
  }
};

const onItemClick = (item: T) => {
  editRelation(item.id);
};

const getSearchedItems = async (string = "") => {
  try {
    loading.value = true
    const { data } = await client.get(`/api/admin/${getModuleUrlPart(moduleKey)}`, {
      params: {
        search: string,
        with: module.value?.relations,
      },
    });
    searchedItems.value = data;
  } catch (e) {
    console.log(e);
  } finally {
    loading.value = false
  }
};

watch(
  search,
  debounce((newValue) => {
    if (!newValue) return;
    getSearchedItems(newValue);
  }, 300)
);
</script>
