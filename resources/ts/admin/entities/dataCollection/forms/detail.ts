import { createFields as linkCreateFields } from "@admin/entities/link"
import { DataCollection } from "@/ts/types/models";
import type {
  IOptionsFieldsFabric,
  ISmartFormField,
} from "@admin/shared/types";
import { computed, defineAsyncComponent, markRaw } from "vue";

export const createFields = (
  options?: IOptionsFieldsFabric<DataCollection>
) => {
  const RelationAutocomplete = defineAsyncComponent(
    () => import("@admin/shared/components/RelationAutocomplete.vue")
  );

  const RelationTree = defineAsyncComponent(
    () => import("@admin/shared/components/RelationTree.vue")
  );

  const JSONEditor = defineAsyncComponent(
    () => import("@admin/shared/components/JSONEditor.vue")
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
    ...linkCreateFields(undefined, 'link.').fields.value,
    {
      component: markRaw(JSONEditor),
      key: "meta",
      props: {
        title: "Мета",
      },
    },
    {
      component: markRaw(RelationAutocomplete),
      key: "page_id",
      props: {
        autocomplete: "page_id",
        label: "Страница",
        name: "page_id",
        itemValue: "id",
        itemTitle: "link.title",
        moduleKey: "page",
        readonly: !!options?.initialValues?.page_id,
        initialItems: options?.entity?.page
          ? [options?.entity?.page]
          : [],
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
          moduleKey: "dataCollection",
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
        component: markRaw(RelationTable),
        key: "data_entities",
        props: {
          moduleKey: "dataEntity",
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
              key: "pivot.link",
            },
            {
              title: "Действия",
              key: "actions",
              width: 100,
            },
          ],
        },
      }
    );
  }

  return {
    fields,
  };
};
