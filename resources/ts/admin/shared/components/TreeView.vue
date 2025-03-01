<template>
  <div>
    <!-- Поле ввода для поиска по дереву -->
    <v-text-field v-model="search" label="Поиск..." clearable hide-details></v-text-field>

    <!-- Список дерева -->
    <v-list dense>
      <template v-for="item in filteredItems" :key="item.id">
        <v-list-item @click="onNodeClick(item)">
          <template #prepend>
            <v-icon v-if="item.children" @click.stop="toggleExpand(item)">
              {{
                expandedNodes.has(item.id)
                  ? "mdi-chevron-down"
                  : "mdi-chevron-right"
              }}
            </v-icon>
          </template>
          <v-list-item-content>
            <v-list-item-title>{{ getItemTitle(item) }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <!-- Дочерние элементы (рекурсивный рендеринг) -->
        <div v-if="item.children && expandedNodes.has(item.id)" class="pl-4">
          <TreeView :items="item.children" @item-click="onNodeClick" />
        </div>
      </template>
    </v-list>
  </div>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSet<T>">
import { ref, computed, defineProps, defineEmits } from "vue";
import type { IBaseEntity, INestedSet, ITreeViewProps } from "../types";
import { useItems } from "../composables/useItems";
import { get } from "lodash";

// Пропсы
const { items = [], itemValue, itemTitle } = defineProps<ITreeViewProps<T>>();

const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });

// Эмит событий
const emit = defineEmits<{
  (e: "item-click", item: T): void;
}>();

// Строка поиска
const search = ref("");

// Управление раскрытием узлов
const expandedNodes = ref<Set<string>>(new Set());

// Функция переключения раскрытия узлов
const toggleExpand = (item: T) => {
  if (expandedNodes.value.has(item.id)) {
    expandedNodes.value.delete(item.id);
  } else {
    expandedNodes.value.add(item.id);
  }
};

// Фильтрация дерева по поиску
const filteredItems = computed(() => {
  const filterTree = (nodes: T[]): T[] => {
    return nodes
      .filter((item) => {
        console.log(getItemTitle(item))
        return getItemTitle(item).toLowerCase().includes(search.value.toLowerCase())
      })
      .map((item) => ({
        ...item,
        children: item.children ? filterTree(item.children) : undefined,
      }));
  };

  return filterTree(items);
});

// Обработчик кликов по узлу
const onNodeClick = (item: T) => {
  emit("item-click", item);
};
</script>
