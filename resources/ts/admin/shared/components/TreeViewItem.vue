<!-- TreeViewItem.vue -->
<template>
    <div>
        <v-list-item
            class="tree-item"
            :style="{ 'margin-left': `${depth * 20}px` }"
            @click="toggle"
            :class="{ 'highlight-match': isMatched }"
        >
            <template #prepend>
                <v-icon
                    v-if="hasChildren"
                    :icon="
                        shouldShowChildren
                            ? 'mdi-chevron-down'
                            : 'mdi-chevron-right'
                    "
                    size="small"
                    class="mr-2"
                />
                <span v-else class="spacer"></span>
            </template>

            <v-list-item-title>
                <span :class="{ 'text-primary': isMatched }">
                    {{ item.link.title }}
                </span>
                <v-chip
                    v-if="item.link.subtitle"
                    size="x-small"
                    class="ml-2"
                    :class="{ 'bg-primary': isMatched }"
                >
                    {{ item.link.subtitle }}
                </v-chip>
            </v-list-item-title>

            <template #append>
                <v-btn
                    variant="text"
                    size="small"
                    :href="item.link.url"
                    target="_blank"
                    @click.stop
                >
                    <v-icon icon="mdi-open-in-new" size="small" />
                </v-btn>
            </template>
        </v-list-item>

        <div v-if="shouldShowChildren" class="children">
            <TreeViewItem
                v-for="child in item.children"
                :key="child.id"
                :item="child"
                :depth="depth + 1"
                :search-query="searchQuery"
                :parent-is-open="isOpen"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from "vue";
import type { PropType } from "vue";

export interface TreeItem {
    id: string;
    meta: Record<string, any> | null;
    _lft: number;
    _rgt: number;
    parent_id: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    template_id: string | null;
    link: {
        id: string;
        title: string;
        subtitle: string | null;
        slug: string;
        url: string;
    };
    children: TreeItem[];
}

const props = defineProps({
    item: {
        type: Object as PropType<TreeItem>,
        required: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
    searchQuery: {
        type: String,
        default: "",
    },
    parentIsOpen: {
        type: Boolean,
        default: false,
    },
});

const isOpen = ref(false);
const wasManuallyToggled = ref(false);

const hasChildren = computed(() => !!props.item.children?.length);

const isMatched = computed(() => {
    if (!props.searchQuery) return false;
    const query = props.searchQuery.toLowerCase();
    return [
        props.item.link.title.toLowerCase(),
        props.item.link.subtitle?.toLowerCase(),
        props.item.link.slug.toLowerCase(),
    ].some((field) => field?.includes(query));
});

const shouldShowChildren = computed(
    () =>
        hasChildren.value &&
        (isOpen.value || (props.searchQuery && !wasManuallyToggled.value))
);

watch(
    () => props.searchQuery,
    async (newVal) => {
        if (newVal) {
            if (!wasManuallyToggled.value) {
                await nextTick();
                isOpen.value = true;
            }
        } else {
            wasManuallyToggled.value = false;
            isOpen.value = false;
        }
    }
);

const toggle = () => {
    if (hasChildren.value) {
        isOpen.value = !isOpen.value;
        wasManuallyToggled.value = true;
    }
};
</script>

<style scoped>
.spacer {
    width: 24px;
    display: inline-block;
}

.children {
    transition: opacity 0.2s ease-in-out;
}

.tree-item {
    cursor: pointer;
    min-height: 32px;
}

.tree-item :deep(.v-list-item--active) {
    background: transparent !important;
}

.tree-item :deep(.v-list-item__overlay) {
    display: none !important;
}

.highlight-match {
    background-color: rgba(var(--v-theme-primary), 0.1);
    border-left: 3px solid rgb(var(--v-theme-primary));
}

.text-primary {
    color: rgb(var(--v-theme-primary)) !important;
    font-weight: 600;
}

.v-icon {
    transition: transform 0.2s ease-in-out;
}
</style>
