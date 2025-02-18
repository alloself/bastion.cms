import type { ISmartFormField } from "@admin/shared/types";
import { ref, computed } from "vue";
import * as yup from 'yup';

export const createFields = () => {
  const showPassword = ref(false);

  const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
  };

  const fields = computed<ISmartFormField[]>(() => [
    {
      component: "v-text-field",
      key: "email",
      props: {
        autocomplete: "username",
        label: "Почта",
        name: "email",
        type: "email",
      },
      rule: yup.string()
        .required("Поле обязательно для заполнения")
        .email("Введите корректный email"),
    },
    {
      component: "v-text-field",
      key: "first_name",
      props: {
        autocomplete: "first_name",
        label: "Имя",
        name: "first_name",
        type: "text",
      },
      rule: yup.string()
        .required("Поле обязательно для заполнения"),
    },
    {
      component: "v-text-field",
      key: "last_name",
      props: {
        autocomplete: "last_name",
        label: "Фамилия",
        name: "last_name",
        type: "text",
      },
      rule: yup.string()
        .required("Поле обязательно для заполнения"),
    }, 
    {
      component: "v-text-field",
      key: "middle_name",
      props: {
        autocomplete: "middle_name",
        label: "Отчество",
        name: "middle_name",
        type: "text",
      },
      rule: yup.string()
        .required("Поле обязательно для заполнения"),
    }
  ]);

  return {
    fields,
    togglePasswordVisibility,
    showPassword,
    readonly: true
  };
};