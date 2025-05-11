<template>
    <div class="app-select">
        <select :value="modelValue" @change="onChange">
            <option
                v-for="(option, optionIndex) in options"
                :value="option.value"
                :key="optionIndex"
            >
                {{ option.text }}
            </option>
        </select>
        <div
            class="app-select__value"
            :class="selectedText ? 'text-dark' : 'text-dark text-opacity-50'"
        >
            {{ selectedText || "Выберите пункт" }}
        </div>
        <div class="app-select__arrow svg-icon">
            <svg><use xlink:href="#arrow-down"></use></svg>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

const props = withDefaults(
    defineProps<{
        modelValue: string;
        options?: {
            value: string | number;
            text: string | number;
        }[];
    }>(),
    {
        modelValue: "",
        options: () => [],
    }
);

const emits = defineEmits(["update:modelValue", "change"]);

const selectedText = computed<string | number>(() => {
    const selectedOptionText = props.options.find(
        (option) => option.value === props.modelValue
    )?.text;
    return selectedOptionText || "";
});

function onChange(event: Event) {
    const target = event.target as HTMLSelectElement;
    emits("update:modelValue", target.value);
    emits("change");
}
</script>

<style lang="scss">
.app-select {
    @apply relative
        w-full;

    &__value {
        @apply py-1.5
            px-3
            pr-8
            border
            border-neutral
            rounded-md
            font-semibold
            pointer-events-none
            transition-all
            duration-300
            whitespace-nowrap
            overflow-hidden
            text-ellipsis
            max-w-full
            bg-white;
    }

    &__arrow {
        @apply absolute
            z-[2]
            right-2.5
            top-1/2
            w-5
            h-5
            -translate-y-1/2
            text-opacity-50
            text-dark;
    }

    select {
        @apply p-0
            m-0
            w-full
            h-full
            absolute
            inset-0
            z-[5]
            opacity-0
            font-semibold;

        &:focus ~ .app-select__value {
            @apply border-brand;
        }
    }
}
</style>
