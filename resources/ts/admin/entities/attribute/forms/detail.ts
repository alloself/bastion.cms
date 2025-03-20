import type { ISmartFormField } from "@admin/shared/types";
import { computed } from "vue";

export const createFields = () => {
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
      key: "key",
      props: {
        autocomplete: "key",
        label: "Ключ",
        name: "key",
        type: "text",
      },
    },
  ]);

  return {
    fields,
  };
};