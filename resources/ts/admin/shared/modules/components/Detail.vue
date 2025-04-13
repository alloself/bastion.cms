<template>
    <v-card
        outlined
        rounded="0"
        flat
        :class="detailClass"
        :max-height="`calc(100svh - ${mainRect.top}px`"
        :loading="loading"
        class="h-100 d-flex flex-column overflow-auto w-100"
    >
        <v-card-text class="form-wrapper">
            <smart-form
                :loading="loading"
                :fields="fields"
                :readonly="readonly"
                v-model:form="form"
                :initial-values="initialValues"
            ></smart-form>
        </v-card-text>

        <div class="actions-wrapper">
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn
                    v-for="(action, index) in filteredActions"
                    :key="index"
                    v-bind="action.props"
                    :loading="loading"
                    @click="action.action"
                    >{{ action.title }}</v-btn
                >
                <v-badge color="info" :content="history.length" v-if="id">
                    <v-btn :loading="loading" @click="openHistoryBottomSheet">
                        История
                    </v-btn>
                </v-badge>

                <v-spacer></v-spacer>
                <v-btn @click="router.go(-1)" v-if="!modal">Назад</v-btn>
            </v-card-actions>
        </div>
    </v-card>
    <v-dialog v-model="showConfirmDelete" max-width="450" persistent>
        <v-card
            prepend-icon="mdi-alert"
            title="Вы действительно хотите удалить?"
        >
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn @click="showConfirmDelete = false"> Отмена </v-btn>

                <v-btn @click="onConfirmDelete" color="red"> Удалить </v-btn>
            </template>
        </v-card>
    </v-dialog>
    <history-bottom-sheet
        v-model="showHistory"
        :history="history"
        @restore="onRestore"
    />
</template>
<script setup lang="ts" generic="T extends IBaseEntity">
import type {
    IBaseEntity,
    IDetailProps,
    ISmartFormField,
    TCreateFields,
    THistoryItem,
} from "@admin/shared/types";
import { loading, client } from "@admin/shared/api/axios";
import { useRouter } from "vue-router";
import { useLayout } from "vuetify";
import { capitalize, computed, onMounted, ref, watch } from "vue";
import type { FormContext } from "vee-validate";
import { SmartForm, HistoryBottomSheet } from "@admin/shared/components";
import { useFormSubmit } from "../../composables";
import { getModuleUrlPart } from "..";

const { mainRect } = useLayout();
const router = useRouter();

const {
    modal = false,
    id = undefined,
    initialValues = {},
    module,
    detailClass,
} = defineProps<IDetailProps<T>>();

const emit = defineEmits<{
    (e: "create", value: T): void;
    (e: "update", value: T): void;
    (e: "close"): void;
    (e: "delete"): void;
}>();

const form = ref<FormContext>();
const createFields = ref<TCreateFields<T>>();
const fields = ref<ISmartFormField[]>([]);
const showConfirmDelete = ref(false);
const showHistory = ref(false);
const readonly = ref(false);
const firstLoading = ref(true);

const onReset = () => form.value?.resetForm(initialValues);
const onClose = () => emit("close");

const setupFieldsConstructor = async (key: string) => {
    const module = await import(`@admin/entities/${key}/index.ts`);
    createFields.value = module.createFields || module.default;
};

const initializeFields = async (data?: T) => {
    if (!createFields.value) return;
    const context = data
        ? { entity: data, initialValues }
        : { initialValues: initialValues };

    const cratedFields = await createFields.value(context);
    readonly.value = cratedFields.readonly || false;
    fields.value = cratedFields.fields?.value || [];
};

const getEntity = async () => {
    try {
        loading.value = true;
        const { data } = await client.get<T>(
            `/api/admin/${getModuleUrlPart(module.url || module.key)}/${id}`,
            {
                params: { with: module.relations },
            }
        );

        await initializeFields(data);

        form.value?.resetForm({ values: data, errors: {} });
    } finally {
        loading.value = false;
    }
};

const onCreate = async () => {
    const { handler } = useFormSubmit(async () => {
        const { data } = await client.post<T>(
            `/api/admin/${getModuleUrlPart(module.url || module.key)}`,
            {
                ...form.value?.values,
                with: module.relations || [],
            }
        );

        if (modal) {
            emit("create", data);
        } else {
            const routeName = `${capitalize(module.key)}Detail`;
            console.log(routeName)
            if (router.hasRoute(routeName)) {
                router.push({ name: routeName, params: { id: data.id } });
            }
        }
    }, form);

    await handler();
};

const onUpdate = async () => {
    const { handler } = useFormSubmit(async () => {
        const { data } = await client.patch<T>(
            `/api/admin/${getModuleUrlPart(module.url || module.key)}/${id}`,
            {
                ...form.value?.values,
                with: module.relations || [],
            }
        );

        const values = { ...initialValues, ...data };

        form.value?.resetForm({ values }, { force: true });

        modal && emit("update", values);
    }, form);

    await handler();
};

const onDelete = async () => {
    if (!id) return;

    await client.delete(
        `/api/admin/${getModuleUrlPart(module.url || module.key)}/${id}`
    );

    if (modal) {
        emit("delete");
    } else {
        router.push({ name: `${capitalize(module.key)}List` });
    }
};

const onConfirmDelete = () => {
    onDelete();
    showConfirmDelete.value = false;
};

const allActions = ref([
    {
        title: "Сбросить",
        condition: () => !!id && !readonly.value,
        action: onReset,
        props: { color: "warning" },
    },
    {
        title: "Обновить",
        condition: () => !!id && !readonly.value,
        action: onUpdate,
        props: { color: "primary" },
    },
    {
        title: "Удалить",
        condition: () => !!id,
        action: () => (showConfirmDelete.value = true),
        props: { color: "red" },
    },
    {
        title: "Создать",
        condition: () => !id,
        action: onCreate,
        props: { color: "primary" },
    },
    {
        title: "Закрыть",
        condition: () => modal,
        action: onClose,
        props: { color: "primary" },
    },
]);

const filteredActions = computed(() =>
    allActions.value.filter((action) =>
        typeof action.condition === "function"
            ? action.condition()
            : action.condition
    )
);

const history = computed<THistoryItem[]>(() => form.value?.values.audits || []);

const openHistoryBottomSheet = () => {
    showHistory.value = true;
};

const onRestore = (values: Partial<T>) => {
    const tryParseJson = (value: unknown): unknown => {
        if (typeof value !== "string") return value;

        try {
            const parsed = JSON.parse(value);
            return typeof parsed === "object" ? parsed : value;
        } catch {
            return value;
        }
    };

    const deepProcessObject = (
        obj: Record<string, any>
    ): Record<string, any> => {
        if (!obj || typeof obj !== "object") return obj;

        return Object.entries(obj).reduce((acc, [key, value]) => {
            const processedValue = Array.isArray(value)
                ? value.map((item) =>
                      typeof item === "object"
                          ? deepProcessObject(item)
                          : tryParseJson(item)
                  )
                : typeof value === "object"
                ? deepProcessObject(value)
                : tryParseJson(value);

            return { ...acc, [key]: processedValue };
        }, {});
    };

    try {
        const processedValues = deepProcessObject(values);
        form.value?.resetForm({ values: processedValues });
    } catch (e) {
        form.value?.resetForm({ values });
    }

    onUpdate();
};

onMounted(async () => {
    await setupFieldsConstructor(module.key);

    if (id) {
        await getEntity();
    } else {
        await initializeFields();
    }
});

watch(
    () => id,
    (newVal) => {
        newVal && getEntity();
    }
);
</script>
<style lang="scss" scoped>
.actions-wrapper {
    position: sticky;
    bottom: 0;
    z-index: 10;
    background: rgba(var(--v-theme-surface), 0.9);
    backdrop-filter: blur(5px);

    .v-card-actions {
        padding: 12px 16px;
    }
}

.form-wrapper {
    overflow: hidden;
    padding: 0;
}
</style>
