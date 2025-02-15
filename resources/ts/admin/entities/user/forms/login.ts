import type { ISmartFormField } from "@admin/shared/types";
import { ref, computed } from "vue";
import * as yup from 'yup';

export const useLoginFormFields = () => {
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
                prependIcon: "mdi-email-outline",
                type: "email",
            },
            rule: yup.string()
                .required("Поле обязательно для заполнения")
                .email("Введите корректный email"),
        },
        {
            component: "v-text-field",
            key: "password",
            props: {
                autocomplete: "current-password",
                appendInnerIcon: showPassword.value ? "mdi-eye-off" : "mdi-eye",
                label: "Пароль",
                name: "password",
                prependIcon: "mdi-lock-outline",
                type: showPassword.value ? "text" : "password",
            },
            events: {
                "click:appendInner": togglePasswordVisibility,
            },
            rule: yup.string()
                .required("Поле обязательно для заполнения")
                .min(8, "Пароль должен содержать минимум 8 символов")
        },
    ]);

    return {
        fields,
        togglePasswordVisibility,
        showPassword
    };
};