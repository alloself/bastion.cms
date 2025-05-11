<template>
    <form @submit.prevent="onSubmit">
        <Field name="email" v-slot="{ value, handleChange, errors }">
            <div
                class="app-form-field mb-4"
                :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-right' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false"
            >
                <input
                    type="email"
                    class="app-form-control text-[16px] font-normal"
                    autocomplete="username"
                    :value="value"
                    @input="handleChange"
                    placeholder="Эл. почта"
                />
            </div>
        </Field>
        <Field name="password" v-slot="{ value, handleChange, errors }">
            <div
                class="app-form-field mb-4"
                :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-right' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false"
            >
                <input
                    type="password"
                    class="app-form-control text-[16px] font-normal"
                    autocomplete="current-password"
                    :value="value"
                    @input="handleChange"
                    placeholder="Пароль"
                />
            </div>
        </Field>
        <div class="mb-12 flex items-center gap-7">
            <button type="submit" class="app-button app-button--secondary">
                Войти
            </button>
            <a
                href="/vosstanovlenie-parolia"
                class="text-dark text-opacity-40 font-semibold underline hover:no-underline"
                >Забыли пароль?</a
            >
        </div>
    </form>
</template>

<script setup lang="ts">
import axios from "axios";
import { useForm, Field } from "vee-validate";
import * as yup from "yup";

const schema = yup.object().shape({
    email: yup.string().required("Поле обязательно для заполнения"),
    password: yup.string().required("Поле обязательно для заполнения"),
});
const form = useForm({
    validationSchema: schema,
    initialValues: {
        name: "",
    },
});

async function onSubmit() {
    const validateRezult = await form.validate();
    if (!validateRezult.valid) {
        return;
    }

    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/login", form.values);

    window.location.href = '/moi-dannye'
}
</script>
