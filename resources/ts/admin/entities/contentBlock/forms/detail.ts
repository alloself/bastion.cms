import { ContentBlock } from "@/ts/types/models";
import * as yup from "yup";
import type {
    IOptionsFieldsFabric,
    ISmartFormField,
} from "@admin/shared/types";
import { computed, markRaw, defineAsyncComponent } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<ContentBlock>) => {
    const WYSIWYGEditor = defineAsyncComponent(
        () => import("@admin/shared/components/WYSIWYGEditor.vue")
    );

    const RelationAutocomplete = defineAsyncComponent(
        () => import("@admin/shared/components/RelationAutocomplete.vue")
    );
    const RelationTree = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTree.vue")
    );

    const RelationTable = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTable.vue")
    );

    const fields = computed<ISmartFormField[]>(() => [
        {
            component: "v-text-field",
            key: "name",
            props: {
                autocomplete: "name",
                label: "Название",
                name: "name",
                type: "text",
            },
        },
        {
            component: "v-text-field",
            key: "link.title",
            props: {
                autocomplete: "title",
                label: "Заголовок",
                name: "title",
                type: "text",
            },
            rule: yup.string().required("Поле обязательно для заполнения"),
        },
        {
            component: "v-text-field",
            key: "link.subtitle",
            props: {
                autocomplete: "subtitle",
                label: "Подзаголовок",
                name: "subtitle",
                type: "text",
            },
        },
        {
            component: markRaw(RelationTable),
            key: "attributes",
            props: {
                title: "Атрибуты",
                moduleKey: "attribute",
                itemTitle: "name",
                morph: true,
                ordered: true,
                headers: [
                    {
                        title: "Название",
                        key: "name",
                    },
                    {
                        title: "Ключ",
                        key: "key",
                    },
                    {
                        title: "Значение",
                        key: "pivot.value",
                    },
                    {
                        title: "Приоритет",
                        key: "order",
                    },
                    {
                        title: "Действия",
                        key: "actions",
                        width: 100,
                    },
                ],
            },
        },
        {
            component: markRaw(RelationAutocomplete),
            key: "template_id",
            props: {
                autocomplete: "template_id",
                label: "Шаблон",
                name: "template_id",
                itemValue: "id",
                itemTitle: "name",
                moduleKey: "template",
                initialItems: options?.entity?.template
                    ? [options?.entity?.template]
                    : [],
            },
        },
        {
            component: markRaw(RelationAutocomplete),
            key: "parent_id",
            props: {
                autocomplete: "parent_id",
                label: "Родительский блок",
                name: "parent_id",
                itemValue: "id",
                itemTitle: "name",
                moduleKey: "contentBlock",
                readonly: !!options?.initialValues?.parent_id,
                initialItems: options?.entity?.parent
                    ? [options?.entity?.parent]
                    : [],
            },
        },
        {
            component: markRaw(WYSIWYGEditor),
            key: "content",
        },
        {
            component: markRaw(RelationTable),
            key: "files",
            props: {
                title: "Файлы",
                moduleKey: "file",
                itemTitle: "name",
                morph: true,
                ordered: true,
                headers: [
                    {
                        title: "Название",
                        key: "name",
                    },

                    {
                        title: "Ключ",
                        key: "pivot.key",
                    },
                    {
                        title: "Приоритет",
                        key: "order",
                    },
                    {
                        title: "Ссылка",
                        key: "url",
                    },
                ],
            },
        },
        {
            component: markRaw(RelationTable),
            key: "images",
            props: {
                title: "Изображения",
                moduleKey: "image",
                itemTitle: "name",
                morph: true,
                ordered: true,
                headers: [
                    {
                        title: "Название",
                        key: "name",
                    },
                    {
                        title: "Ключ",
                        key: "pivot.key",
                    },
                    {
                        title: "Приоритет",
                        key: "order",
                    },
                    {
                        title: "Ссылка",
                        key: "url",
                    },
                    {
                        title: "Првеью",
                        key: "preview",
                        width: 100,
                    },
                ],
            },
        },
    ]);

    if (options?.entity?.id) {
        fields.value.push({
            component: markRaw(RelationTree),
            key: "children",
            props: {
                initialValues: { parent_id: options.entity.id },
                moduleKey: "contentBlock",
                itemTitle: "name",
            },
        });
    }

    return {
        fields,
    };
};
