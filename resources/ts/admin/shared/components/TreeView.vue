<template>
    <div class="tree-view-container">
        <v-text-field
            v-model="searchQuery"
            density="compact"
            placeholder="Поиск"
            prepend-inner-icon="mdi-magnify"
            clearable
            hide-details
        />

        <v-list density="compact" class="tree-view">
            <TreeViewItem
                v-for="item in filteredItems"
                :key="item.id"
                :item="item"
                :depth="0"
                :search-query="searchQuery"
            />
        </v-list>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { PropType } from "vue";
import TreeViewItem from "./TreeViewItem.vue";
import type { TreeItem } from "./TreeViewItem.vue";

const props = defineProps({
    items: {
        type: Array as PropType<TreeItem[]>,
        required: true,
    },
});

const searchQuery = ref("");

const filteredItems = computed(() => {
    if (!searchQuery.value) return props.items;
    return filterItems(props.items, searchQuery.value.toLowerCase());
});

function filterItems(items: TreeItem[], query: string): TreeItem[] {
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

function checkItemMatches(item: TreeItem, query: string): boolean {
    return (
        item.link.title.toLowerCase().includes(query) ||
        (item.link.subtitle &&
            item.link.subtitle.toLowerCase().includes(query)) ||
        item.link.slug.toLowerCase().includes(query)
    );
}
</script>
