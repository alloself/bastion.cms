import type { ISmartFormField } from "@admin/shared/types";
import { computed, markRaw, defineAsyncComponent } from "vue";

export const createFields = () => {
  const CodeEditor = defineAsyncComponent(
    () => import("@admin/shared/components/CodeEditor.vue")
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
      component: markRaw(CodeEditor),
      key: "value",
    },
  ]);

  return {
    fields,
  };
};