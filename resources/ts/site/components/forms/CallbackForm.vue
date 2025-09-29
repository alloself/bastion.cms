<template>
    <form
        class="relative"
        @submit.prevent="onSubmit"
        :class="{ 'opacity-60 pointer-events-none': state.loading }"
    >
        <div
            v-if="state.loading"
            class="absolute z-[1] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2"
        >
            <div
                class="svg-icon animate-spin w-14 h-14 text-brand text-opacity-80"
            >
                <svg><use xlink:href="#spinner"></use></svg>
            </div>
        </div>
        <div class="app-form-field mb-4">
            <input type="text" class="app-form-control" placeholder="Имя" />
        </div>
        <div class="app-form-field mb-6">
            <input type="tel" class="app-form-control" placeholder="Телефон" />
        </div>
        <div class="app-form-field mb-6">
            <input type="email" class="app-form-control" placeholder="Email" />
        </div>
        <div class="app-form-field mb-6">
            <textarea
                class="app-form-control h-[116px]"
                placeholder="Комментарий"
            ></textarea>
        </div>
        <label class="app-form-field flex items-center gap-3 mb-6">
            <div class="app-checkmark">
                <input type="checkbox" />
                <div class="svg-icon size-5">
                    <svg><use xlink:href="#checkmark"></use></svg>
                </div>
            </div>
            <div class="text-simple text-simple--dark">
                Я согласен на обработку личных данных
            </div>
        </label>
        <div class="flex gap-1">
            <button type="submit" class="app-button app-button--primary">
                Отправить данные
            </button>
            <button
                type="submit"
                class="app-button app-button--primary app-button--square"
            >
                +
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import { modalToggle } from "@site/composables/useModal";
import { useNotifications } from "@site/composables/useNotification";

const state = reactive({
    loading: false,
});

async function wait(ms: number) {
    return new Promise((res) => {
        setTimeout(() => {
            res("");
        }, ms);
    });
}

async function onSubmit() {
    state.loading = true;

    await wait(2000);

    useNotifications.show({
        icon: "success",
        type: "success",
        text: "Заявка успешно принята",
    });

    modalToggle("callbackModal");
}
</script>
