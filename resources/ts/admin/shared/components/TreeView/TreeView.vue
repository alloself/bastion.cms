<template>
    <div class="tree-view-container">
        <v-text-field
            v-model="search"
            density="compact"
            placeholder="Поиск"
            prepend-inner-icon="mdi-magnify"
            clearable
            hide-details
            @click:clear="search = ''"
        />

        <v-list density="compact" class="tree-view">
            <TreeViewItem
                v-for="item in filteredItems"
                :key="item.id"
                :item="item"
                :depth="0"
                :search="search"
                :getItemValue="getItemValue"
                :getItemTitle="getItemTitle"
                :onItemClick="onItemClick"
            />
        </v-list>
    </div>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSet<T>">
import { ref, computed } from "vue";
import TreeViewItem from "./TreeViewItem.vue";
import { IBaseEntity, INestedSet, ITreeViewProps } from "../../types";
import { useItems } from "../../composables";

const { items = [], itemTitle, itemValue } = defineProps<ITreeViewProps<T>>();
const emit = defineEmits<{
    "item:click": [value: T];
}>();

const { getItemValue, getItemTitle } = useItems<T>({ itemTitle, itemValue });

const search = ref("");

const filteredItems = computed(() => {
    if (!search.value) return items;
    return filterItems(items, search.value.toLowerCase());
});

function filterItems(items: T[], query: string): T[] {
    return items
        .map((item) => ({ ...item }))
        .filter((item) => {
            const matches = checkItemMatches(item, query);
            if (item.children) {
                item.children = filterItems(item.children, query);
            }
            return matches || (item.children && item.children.length > 0);
        });
}

function checkItemMatches(item: T, query: string): boolean {
    return getItemTitle(item).toLowerCase().includes(query);
}

const onItemClick = (item: T) => {
    emit("item:click", item);
};
</script>
