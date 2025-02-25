<template>
    <relation-card :title="module?.title" :icon="module?.icon">
        <v-treeview
            v-model:selected="selected"
            :items="modelValue"
            class="flex-1-0"
            item-title="name"
            item-value="id"
            select-strategy="classic"
            return-object
        ></v-treeview>
    </relation-card>
</template>

<script setup lang="ts" generic="T extends IBaseEntity & INestedSet<T>">
import { ref } from "vue";
import { useModule } from "../composables";
import type { IBaseEntity, INestedSet, IRelationTreeProps } from "../types";
import RelationCard from "./RelationCard.vue";

const {
    moduleKey,
    modelValue = [],
    initialValues,
    itemTitle,
} = defineProps<IRelationTreeProps<T>>();

const selected = ref([]);

const { module } = useModule(moduleKey);
</script>
