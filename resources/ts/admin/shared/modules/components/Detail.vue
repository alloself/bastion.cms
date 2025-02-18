<template>
    <v-card
        outlined
        rounded="0"
        flat
        :max-height="`calc(100svh - ${mainRect.top}px)`"
        :loading="loading"
        class="h-100 d-flex flex-column overflow-auto w-100"
    >
        <v-card-text>
            <smart-form
                :loading="loading"
                :fields="fields"
                :readonly="readonly"
                v-model:form="form"
                :initial-values="initialValues"
            ></smart-form>
        </v-card-text>
        <v-spacer></v-spacer>
        <div class="actions-wrapper">
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn
                    v-for="(action, index) in filteredActions"
                    :key="index"
                    v-bind="action.props"
                    @click="action.action"
                    >{{ action.title }}</v-btn
                >
                <v-spacer></v-spacer>
                <v-btn @click="router.go(-1)" v-if="!modal">Назад</v-btn>
            </v-card-actions>
        </div>
    </v-card>
    <v-dialog v-model="showConfirmDelete" max-width="450" persistent>
        <v-card prepend-icon="mdi-alert" title="Вы действительно хотите удалить?">
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn @click="showConfirmDelete = false"> Отмена </v-btn>

                <v-btn @click="onConfirmDelete" color="red"> Удалить </v-btn>
            </template>
        </v-card>
    </v-dialog>
</template>
<script setup lang="ts" generic="T extends IBaseEntity">
import type {
    IBaseEntity,
    IDetailProps,
    ISmartFormField,
    TCreateFields,
} from "@admin/shared/types";
import { loading, client } from "@admin/shared/api/axios";
import { useRouter } from "vue-router";
import { useLayout } from "vuetify";
import { capitalize, computed, onMounted, ref, watch } from "vue";
import type { FormContext } from "vee-validate";
import { SmartForm } from "@admin/shared/components";

const { mainRect } = useLayout();
const router = useRouter();

const {
    modal = false,
    id = undefined,
    initialValues = {},
    module,
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
const readonly = ref(false)

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
    readonly.value = cratedFields.readonly || false
    fields.value = cratedFields.fields?.value || [];
};

const getEntity = async () => {
    try {
        loading.value = true;
        const { data } = await client.get<T>(`/api/admin/${module.key}/${id}`, {
            params: { with: module.relations },
        });
        await initializeFields(data);
        form.value?.resetForm({ values: { ...data, ...initialValues } });
    } finally {
        loading.value = false;
    }
};

const onCreate = async () => {
    if (!form.value) return;

    const { data } = await client.post<T>(`/api/admin/${module.key}`, {
        ...form.value.values,
        with: module.relations,
    });

    if (modal) {
        emit("create", data);
    } else {
        const routeName = `${capitalize(module.key)}Detail`;
        if (router.hasRoute(routeName)) {
            router.push({ name: routeName, params: { id: data.id } });
        }
    }
};

const onUpdate = async () => {
    if (!form.value || !id) return;

    const { data } = await client.patch<T>(`/api/admin/${module.key}/${id}`, {
        ...form.value.values,
        with: module.relations,
    });

    form.value.resetForm({ values: { ...initialValues, ...data } });
    modal && emit("update", data);
};

const onDelete = async () => {
    if (!id) return;

    await client.delete(`/api/admin/${module.key}/${id}`);

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
        action: () => showConfirmDelete.value = true,
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
</style>
