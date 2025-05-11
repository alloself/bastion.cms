<template>
  <form @submit.prevent="onSubmit">
    <Field name="name" v-slot="{ value, handleChange, errors }">
      <div class="app-form-field mb-4" :aria-label="errors.join(',')"
        :data-cooltipz-dir="errors.length ? 'top-right' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
        :data-cooltipz-visible="errors.length ? true : false">
        <input type="text" class="app-form-control text-[16px] font-normal" autocomplete="username" :value="value"
          @input="handleChange" placeholder="Ваше имя" />
      </div>
    </Field>
    <Field name="email" v-slot="{ value, handleChange, errors }">
      <div class="app-form-field mb-4" :aria-label="errors.join(',')"
        :data-cooltipz-dir="errors.length ? 'top-right' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
        :data-cooltipz-visible="errors.length ? true : false">
        <input type="email" class="app-form-control text-[16px] font-normal" autocomplete="email" :value="value"
          @input="handleChange" placeholder="Эл. почта" />
      </div>
    </Field>
    <Field name="phone" v-slot="{ value, handleChange, errors }">
      <div class="app-form-field mb-4" :aria-label="errors.join(',')"
        :data-cooltipz-dir="errors.length ? 'top-right' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
        :data-cooltipz-visible="errors.length ? true : false">
        <input type="text" class="app-form-control text-[16px] font-normal" autocomplete="email" :value="value"
          @input="handleChange" placeholder="Телефон" />
      </div>
    </Field>
    <Field name="password" v-slot="{ value, handleChange, errors }">
      <div class="app-form-field mb-4" :aria-label="errors.join(',')"
        :data-cooltipz-dir="errors.length ? 'top-right' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
        :data-cooltipz-visible="errors.length ? true : false">
        <input type="password" class="app-form-control text-[16px] font-normal" :value="value" @input="handleChange"
          placeholder="Пароль" autocomplete="new-password" />
      </div>
    </Field>
    <Field name="password_confirmation" v-slot="{ value, handleChange, errors }">
      <div class="app-form-field mb-4" :aria-label="errors.join(',')"
        :data-cooltipz-dir="errors.length ? 'top-right' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
        :data-cooltipz-visible="errors.length ? true : false">
        <input type="password" class="app-form-control text-[16px] font-normal" :value="value" @input="handleChange"
          placeholder="Подтвердите пароль" autocomplete="new-password" />
      </div>
    </Field>
    <Field name="accept" v-slot="{ value, handleChange, errors }">
      <label class="mb-10 flex items-center text-[12px] text-black">
        <div class="app-checkmark mr-2" :aria-label="errors.join(',')"
          :data-cooltipz-dir="errors.length ? 'bottom-left' : ''" :data-cooltipz-theme="errors.length ? 'danger' : ''"
          :data-cooltipz-visible="errors.length ? true : false">
          <input type="checkbox" :checked="value" @change="handleChange" />
          <div class="svg-icon">
            <svg>
              <use xlink:href="#checkmark"></use>
            </svg>
          </div>
        </div>
        <div>
          я согласен с условиями
          <a href="/politika-konfidencialnosti" class="underline hover:no-underline">политики конфиденциальности</a>
        </div>
      </label>
    </Field>
    <div class="mb-12 flex items-center gap-7">
      <button type="submit" class="app-button app-button--secondary">
        Зарегистрироваться
      </button>
      <a href="/login"
        class="text-dark text-opacity-40 font-semibold underline hover:no-underline">Уже есть аккаунт?</a>
    </div>
  </form>
</template>

<script setup lang="ts">
import axios from "axios";
import { useForm, Field } from "vee-validate";
import * as yup from "yup";

const schema = yup.object().shape({
  name: yup
    .string()
    .min(8, "Поле должно быть не короче 8 символов")
    .required("Поле обязательно для заполнения"),
  email: yup
    .string()
    .email('Поле должно быть действительным электронным адресом')
    .required("Поле обязательно для заполнения"),
  phone: yup
    .string()
    .required("Поле обязательно для заполнения"),
  password: yup
    .string()
    .min(8, "Поле должно быть не короче 8 символов")
    .required("Поле обязательно для заполнения"),
  password_confirmation: yup
    .string()
    .required("Поле обязательно для заполнения")
    .oneOf([yup.ref("password")], "Пароли не совпадают"),
  accept: yup.string().required("Поле обязательно для заполнения"),
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
  await axios.post("/register", form.values);
  window.location.href = '/moi-dannye'
}
</script>
