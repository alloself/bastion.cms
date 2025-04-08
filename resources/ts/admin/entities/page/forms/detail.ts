import type { Page } from "@/ts/types/models";
import { createFields as linkCreateFields } from "@admin/entities/link"
import type {
    IOptionsFieldsFabric,
    ISmartFormField,
} from "@admin/shared/types";
import { computed, markRaw, defineAsyncComponent } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<Page>) => {
    const RelationAutocomplete = defineAsyncComponent(
        () => import("@admin/shared/components/RelationAutocomplete.vue")
    );
    const RelationTree = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTree.vue")
    );

    const RelationTable = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTable.vue")
    );

    const JSONEditor = defineAsyncComponent(
        () => import("@admin/shared/components/JSONEditor.vue")
    );

    const fields = computed<ISmartFormField[]>(() => [
        ...linkCreateFields(undefined, 'link.').fields.value,
        {
            component: markRaw(JSONEditor),
            key: "meta",
            props: {
                title: "Мета",
            },
        },
        {
            component: "v-checkbox",
            key: "index",
            props: {
                autocomplete: "index",
                label: "Главная страница",
                name: "index",
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
                label: "Родительская страница",
                name: "parent_id",
                itemValue: "id",
                itemTitle: "link.title",
                moduleKey: "page",
                readonly: !!options?.initialValues?.parent_id,
                initialItems: options?.entity?.parent
                    ? [options?.entity?.parent]
                    : [],
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
        fields.value.push(
            {
                component: markRaw(RelationTree),
                key: "children",
                props: {
                    initialValues: { parent_id: options.entity.id },
                    moduleKey: "page",
                    orderable: false,
                    itemTitle: "link.title",
                },
            },
            {
                component: markRaw(RelationTree),
                key: "content_blocks",
                props: {
                    moduleKey: "contentBlock",
                    itemTitle: "name",
                    morph: true,
                    ordered: true,
                },
            },
            {
                component: markRaw(RelationTree),
                key: "data_collections",
                props: {
                    initialValues: { page_id: options.entity.id },
                    moduleKey: "dataCollection",
                    itemTitle: "name",
                    morph: true,
                    ordered: true,
                    pivot: {
                        order: 0,
                        key: '',
                        paginate: false
                    }
                },
            }
        );
    }
    return {
        fields,
    };
};
