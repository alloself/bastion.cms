<template>
    <form class="flex flex-col gap-7 lg:max-w-[420px]" @submit.prevent="onSubmit">
        <label>
            <div class="text-dark text-opacity-50 text-[14px]">Введите новый пароль:</div>
            <input type="password" class="app-form-control text-[20px]" value="test_password">
        </label>
        <label>
            <div class="text-dark text-opacity-50 text-[14px]">Повторите пароль:</div>
            <input type="password" class="app-form-control text-[20px]" value="test_password">
        </label>
        <div class="flex flex-col flex-wrap gap-4 sm:flex-row sm:items-center md:col-span-2">
            <button type="submit" class="app-button app-button--dark">Сохранить</button>
            <a
                href="#"
                @click.prevent="$router.go(-1)"
                class="app-button app-button--secondary xl:hidden"
            >вернуться назад</a>
        </div>
    </form>
</template>

<script setup lang="ts">
async function onSubmit() {}
</script>