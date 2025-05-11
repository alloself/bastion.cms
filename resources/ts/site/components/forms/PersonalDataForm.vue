<template>
    <form class="grid gap-7 md:grid-cols-2" @submit.prevent="onSubmit">
        <Field name="last_name" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-left' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <div class="text-dark text-opacity-50 text-[14px]">Фамилия:</div>
                <input type="text" class="app-form-control text-[16px] font-normal" autocomplete="last_name"
                    :value="value" @input="handleChange" />
            </div>
        </Field>
        <Field name="first_name" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-left' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <div class="text-dark text-opacity-50 text-[14px]">Имя:</div>
                <input type="text" class="app-form-control text-[16px] font-normal" autocomplete="first_name"
                    :value="value" @input="handleChange" />
            </div>
        </Field>
        <Field name="middle_name" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-left' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <div class="text-dark text-opacity-50 text-[14px]">Отчество:</div>
                <input type="text" class="app-form-control text-[16px] font-normal" autocomplete="middle_name"
                    :value="value" @input="handleChange" />
            </div>
        </Field>
        <Field name="phone" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-left' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <div class="text-dark text-opacity-50 text-[14px]">Телефон:</div>
                <input type="tel" class="app-form-control text-[16px] font-normal" autocomplete="phone" :value="value"
                    @input="handleChange" />
            </div>
        </Field>
        <Field name="email" v-slot="{ value, handleChange, errors }">
            <div class="app-form-field mb-4" :aria-label="errors.join(',')"
                :data-cooltipz-dir="errors.length ? 'top-left' : ''"
                :data-cooltipz-theme="errors.length ? 'danger' : ''"
                :data-cooltipz-visible="errors.length ? true : false">
                <div class="text-dark text-opacity-50 text-[14px]">E-mail:</div>
                <input type="email" class="app-form-control text-[16px] font-normal" autocomplete="email" :value="value"
                    @input="handleChange" />
            </div>
        </Field>
        <div class="flex flex-col flex-wrap gap-4 sm:flex-row sm:items-center md:col-span-2 ">
            <button type="submit" class="app-button app-button--dark">Сохранить</button>
            <a href="#" @click.prevent="$router.go(-1)" class="app-button app-button--secondary xl:hidden">вернуться
                назад</a>


            <button class="app-button app-button--secondary" @click="onLogout">Выйти</button>
        </div>
    </form>
</template>

<script setup lang="ts">
import axios from 'axios';
import { useForm, Field } from "vee-validate";
import * as yup from "yup";

const { user } = defineProps<{ user: Record<string, unknown> }>()

const schema = yup.object().shape({
    first_name: yup
        .string()
        .min(3, "Поле должно быть не короче 8 символов")
        .required("Поле обязательно для заполнения"),
    last_name: yup
        .string()
        .min(3, "Поле должно быть не короче 8 символов")
        .required("Поле обязательно для заполнения"),
    middle_name: yup
        .string()
        .min(3, "Поле должно быть не короче 8 символов"),
    phone: yup
        .string()
        .min(8, "Поле должно быть не короче 8 символов"),
    email: yup
        .string()
        .email("Поле должно быть действительным электронным адресом")
        .required("Поле обязательно для заполнения"),
});
const form = useForm({
    validationSchema: schema,
    initialValues: {
        ...user,
    },
});

const onSubmit = () => {

}


const onLogout = async () => {

    await axios.get("/sanctum/csrf-cookie");
    await axios.post("/logout");

    window.location.href = '/login'
}
</script>