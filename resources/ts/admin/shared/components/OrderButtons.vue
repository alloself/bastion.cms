<template>
    <div class="d-flex items-center ga-2 ordered-buttons" @click.stop>
        <v-btn
            icon="mdi-arrow-up-bold-circle-outline"
            size="small"
            @click="onUpdate(getItemOrder(item, morph) + 1)"
            variant="text"
        >
        </v-btn>
        <v-text-field
            class="centered-input"
            hide-details="auto"
            label="Приоритет"
            :model-value="getItemOrder(item,morph)"
            @update:model-value="(v) => onUpdate(Number(v))"
            density="compact"
        >
        </v-text-field>
        <v-btn
            icon="mdi-arrow-down-bold-circle-outline"
            size="small"
            variant="text"
            @click="onUpdate(getItemOrder(item, morph) - 1)"
        >
        </v-btn>
    </div>
</template>

<script setup lang="ts" generic="T extends IOrderedEntity & IBaseEntity">
import { useOrderedEntity } from "../composables/useOrderedEntity";
import { IModule } from "../modules";
import { IBaseEntity, IOrderedEntity } from "../types";

const {
    module,
    item,
    morph = false,
} = defineProps<{
    module: IModule;
    item: T;
    morph?: boolean;
}>();

const emit = defineEmits<{
    "update:order": [value: T];
}>();

const { updateOrder, getItemOrder } = useOrderedEntity(module);

const onUpdate = async (value: number) => {
    const updatedItem = await updateOrder(item, value, morph);

    emit("update:order", updatedItem);
};
</script>
<style lang="scss" scoped>
.ordered-buttons {
    width: 190px;
}
</style>