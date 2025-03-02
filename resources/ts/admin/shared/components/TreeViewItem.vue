<template>
  <div>
    <v-list-item class="tree-item" @click="toggle" :class="{ 'highlight-match': isMatched }"
      :style="{ 'padding-left': depth ? `${16 + (depth === 1 ? 90 : 0) + 20 * depth}px` : '16px' }">
      <template #prepend>
        <v-icon v-if="hasChildren" :icon="shouldShowChildren
          ? 'mdi-chevron-down'
          : 'mdi-chevron-right'
          " size="small" class="mr-2" />
        <v-checkbox v-if="showCheckbox" density="compact" @click.stop hide-details class="mr-2" />
      </template>

      <v-list-item-title>
        <span :class="{ 'text-primary': isMatched }">
          {{ item.link.title }}
        </span>
      </v-list-item-title>

      <template #append>
        <v-btn variant="text" size="small" icon="mdi-open-in-new" @click.stop>
        </v-btn>
      </template>
    </v-list-item>

    <div v-if="shouldShowChildren" class="children">
      <TreeViewItem v-for="child in item.children" :key="child.id" :item="child" :depth="depth + 1"
        :search-query="searchQuery" :parent-is-open="isOpen" />
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
const showCheckbox = computed(() => props.depth === 0)

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
.highlight-match {
  background-color: rgba(var(--v-theme-primary), 0.1);
  border-left: 3px solid rgb(var(--v-theme-primary));
}
</style>
