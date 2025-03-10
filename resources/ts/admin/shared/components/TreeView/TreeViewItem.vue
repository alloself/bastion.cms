<template>
    <div>
        <v-list-item
            class="tree-item"
            @click="toggle"
            :class="{ 'highlight-match': isMatched }"
            :style="{
                'padding-left': depth
                    ? `${16 + (depth === 1 ? 90 : 0) + 20 * depth}px`
                    : '16px',
            }"
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
                <v-checkbox
                    v-if="showCheckbox"
                    density="compact"
                    :model-value="isSelected"
                    @update:model-value="toggleSelection"
                    @click.stop
                    hide-details
                    class="mr-2"
                />
            </template>

            <v-list-item-title>
                <span :class="{ 'text-primary': isMatched }">
                    {{ getItemTitle(item) }}
                </span>
            </v-list-item-title>

            <template #append>
                <v-btn
                    variant="text"
                    size="small"
                    icon="mdi-open-in-new"
                    @click.stop="onItemClick(item)"
                >
                </v-btn>
            </template>
        </v-list-item>

        <div v-if="shouldShowChildren" class="children">
            <TreeViewItem
                v-for="child in item.children"
                :key="child.id"
                :item="child"
                :depth="depth + 1"
                :search="search"
                :parent-is-open="isOpen"
                :getItemTitle="getItemTitle"
                :getItemValue="getItemValue"
                :onItemClick="onItemClick"
            />
        </div>
    </div>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSetEntity<T>">
import { ref, computed, watch, nextTick, ModelRef } from "vue";

import { IBaseEntity, INestedSetEntity, ITreeViewItemProps } from "../../types";

const {
    item,
    depth = 0,
    search = "",
    getItemTitle,
    getItemValue,
    onItemClick,
} = defineProps<ITreeViewItemProps<T>>();

const selectedModel = defineModel("selected", { default: [] }) as ModelRef<T[]>;

const emit = defineEmits<{
    "update:model-value": [value: T[]];
    "update:selected": [value: T[]];
}>();

const isOpen = ref(false);
const wasManuallyToggled = ref(false);

const hasChildren = computed(() => !!item.children?.length);
const showCheckbox = computed(() => depth === 0);
const isSelected = computed(() =>
    selectedModel.value.some(
        (selected) => getItemValue(selected) === getItemValue(item)
    )
);

const isMatched = computed(() => {
    if (!search) return false;
    const query = search.toLowerCase();
    return getItemTitle(item).toLowerCase().includes(query);
});

const shouldShowChildren = computed(
    () =>
        hasChildren.value &&
        (isOpen.value || (search && !wasManuallyToggled.value))
);

watch(
    () => search,
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

const toggleSelection = () => {
    const newValue = [...selectedModel.value];

    const value = isSelected.value
        ? newValue.filter((el) => getItemValue(el) !== getItemValue(item))
        : [...newValue, item];

    selectedModel.value = value;
};
</script>

<style scoped>
.highlight-match {
    background-color: rgba(var(--v-theme-primary), 0.1);
    border-left: 3px solid rgb(var(--v-theme-primary));
}
</style>
