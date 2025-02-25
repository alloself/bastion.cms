<template>
    <v-bottom-sheet v-model="modelValue">
        <v-card title="История изменений">
            <v-card-text>
                <v-timeline side="end" hideOpposite class="history-timeline">
                    <v-timeline-item
                        v-for="(entry, index) in history.toReversed()"
                        :key="index"
                    >
                        <v-card
                            class="pa-2"
                            variant="tonal"
                            :color="getColor(entry.event)"
                        >
                            <v-card-title>
                                {{ formatDate(entry.created_at) }} -
                                {{ entry.user.first_name }}
                                {{ entry.user.last_name }}
                            </v-card-title>
                            <v-card-subtitle>
                                Событие: {{ mapEvent(entry.event) }}
                            </v-card-subtitle>
                            <v-card-text>
                                <div v-if="entry.event === 'created'">
                                    Создан новый объект с ID:
                                    {{ entry.auditable_id }}
                                </div>
                                <div v-else-if="entry.event === 'updated'">
                                    <p class="mb-2">
                                        Обновлены следующие поля:
                                    </p>
                                    <ul class="pl-4">
                                        <li
                                            v-for="(
                                                newValue, field
                                            ) in entry.new_values"
                                            :key="field"
                                        >
                                            <strong>{{ field }}:</strong>
                                            с "{{
                                                entry.old_values
                                                    ? entry.old_values[field]
                                                    : "не задано"
                                            }}" на "{{ newValue }}"
                                        </li>
                                    </ul>
                                </div>
                                <div v-else-if="entry.event === 'deleted'">
                                    Объект с ID {{ entry.auditable_id }} был
                                    удален.
                                </div>
                            </v-card-text>
                            <v-card-actions v-if="entry.event === 'updated'">
                                <v-btn @click="restoreData(entry)"
                                    >Восстановить</v-btn
                                >
                            </v-card-actions>
                        </v-card>
                    </v-timeline-item>
                </v-timeline>
            </v-card-text>
        </v-card>
    </v-bottom-sheet>
</template>
<script setup lang="ts" generic="T">
import type { IHistoryBottomSheetProps, THistoryItem } from "../types";

const modelValue = defineModel<boolean>();

const { history } = defineProps<IHistoryBottomSheetProps>();

const emit = defineEmits<{
    restore: [value: Partial<T>];
    "update:model-value": [value: boolean];
}>();

const formatDate = (date: string | null) => {
    if (!date) {
        return "";
    }
    return new Date(date).toLocaleString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const mapEvent = (event: string) => {
    const eventMap = {
        created: "Создание",
        updated: "Обновление",
        deleted: "Удаление",
    };
    return eventMap[event as keyof typeof eventMap] || "Неизвестное событие";
};

const getColor = (event: string) => {
    const colorMap = {
        created: "green",
        updated: "blue",
        deleted: "red",
    };
    return colorMap[event as keyof typeof colorMap] || "grey";
};

const restoreData = (item: THistoryItem) => {
    emit("restore", item.old_values as Partial<T>);
    emit("update:model-value", false);
};
</script>
<style lang="scss" scoped>
.history-timeline {
    grid-template-columns: 0px max-content 0px;
}
</style>
