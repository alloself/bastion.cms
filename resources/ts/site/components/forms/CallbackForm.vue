<template>
    <form @submit.prevent="onSubmit">
        <div class="app-form-field mb-4">
            <input
                type="text"
                class="app-form-control text-[16px]"
                placeholder="Имя"
            />
        </div>
        <div class="app-form-field mb-6">
            <input
                type="tel"
                class="app-form-control text-[16px]"
                placeholder="Телефон"
            />
        </div>
        <div class="app-form-field mb-6">
            <input
                type="email"
                class="app-form-control text-[16px]"
                placeholder="Email"
            />
        </div>
        <div class="app-form-field mb-6">
            <textarea
                class="app-form-control text-[16px] h-[116px]"
                placeholder="Комментарий"
            ></textarea>
        </div>
        <div>
            <button type="submit" class="app-button app-button--secondary">
                Отправить
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { modalToggle } from "@site/composables/useModal";
import { toast } from "@site/composables/useToast";

async function onSubmit() {
    modalToggle("callbackModal");
    toast.show("success", "Мы свяжемся с вами в ближайшее время");
}
</script>
