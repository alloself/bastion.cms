<template>
    <form @submit.prevent="onSubmit">
        <Field name="email" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-right' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <input type="email" class="app-form-control text-[16px] font-normal" autocomplete="email" :value="value"
                    @input="handleChange" placeholder="Введите Ваш e-mail" />
            </div>
        </Field>
        <div class="mb-12 flex items-center gap-7">
            <button type="submit" class="app-button app-button--secondary">
                Восстановить
            </button>
            <a href="/login" class="text-dark text-opacity-40 font-semibold underline hover:no-underline">Вернуться
                назад</a>
        </div>
    </form>
</template>

<script setup lang="ts">
import axios from "axios";
import { useForm, Field } from "vee-validate";
import * as yup from "yup";

const schema = yup.object().shape({
    email: yup
        .string()
        .email("Поле должно быть действительным электронным адресом")
        .required("Поле обязательно для заполнения"),
});

const form = useForm({
    validationSchema: schema,
    initialValues: {
        email: "",
    },
});

async function onSubmit() {
    const validateRezult = await form.validate();

    if (!validateRezult.valid) {
        return;
    }

    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/forgot-password", form.values);
}
</script>
