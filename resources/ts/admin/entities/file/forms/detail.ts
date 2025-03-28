import type { ISmartFormField } from "@admin/shared/types";
import { computed } from "vue";

export const createFields = () => {
  const fields = computed<ISmartFormField[]>(() => [
    {
      component: "v-file-input",
      key: "file",
      props: {
        autocomplete: "file",
        label: "Файл",
        name: "file",
      },
    },
  ]);

  return {
    fields,
  };
};