<template>
    <codemirror
        v-model="value"
        :indent-with-tab="true"
        :extensions="extensions"
    />
</template>

<script lang="ts" setup>
import { computed } from "vue";
import { Codemirror } from "vue-codemirror";
import { php } from "@codemirror/lang-php";
import { oneDark } from "@codemirror/theme-one-dark";

const extensions = [php(), oneDark];

const { modelValue } = defineProps<{ modelValue?: string }>();
const emits = defineEmits(["update:model-value"]);

const value = computed({
    get: () => modelValue,
    set: (newValue) => emits("update:model-value", newValue),
});
</script>
