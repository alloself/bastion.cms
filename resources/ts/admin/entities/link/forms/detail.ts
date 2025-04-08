import { Link } from "@/ts/types/models";
import type { IOptionsFieldsFabric, ISmartFormField } from "@admin/shared/types";
import { computed } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<Link>, prefix = '') => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: "v-text-field",
      key: `${prefix}title`,
      props: {
        autocomplete: "title",
        label: "Заголовок",
        name: "title",
        type: "text",
      },
    },
    {
      component: "v-text-field",
      key: `${prefix}subtitle`,
      props: {
        autocomplete: "subtitle",
        label: "Подзаголовок",
        name: "subtitle",
        type: "text",
      },
    },
    {
      component: "v-text-field",
      key: `${prefix}url`,
      props: {
        autocomplete: "url",
        label: "Ссылка",
        messages:
          "Генерируется автоматически при создании и изменении заголовка страницы,можно обновить вручную.",
        name: "url",
        type: "text",
        class: "pb-1 mb-2",
      },
    },
  ]);

  return {
    fields,
  };
};