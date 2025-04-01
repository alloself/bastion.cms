import link from "@/ts/admin/shared/forms/link";
import { DataCollection } from "@/ts/types/models";
import type { IOptionsFieldsFabric, ISmartFormField } from "@admin/shared/types";
import { computed, defineAsyncComponent, markRaw } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<DataCollection>) => {

  const RelationAutocomplete = defineAsyncComponent(
    () => import("@admin/shared/components/RelationAutocomplete.vue")
  );

  const RelationTree = defineAsyncComponent(
    () => import("@admin/shared/components/RelationTree.vue")
  );

  const JSONEditor = defineAsyncComponent(
    () => import("@admin/shared/components/JSONEditor.vue")
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
    ...link,
    {
      component: markRaw(JSONEditor),
      key: "meta",
      props: {
        title: "Мета",
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
    );
  }

  return {
    fields,
  };
};