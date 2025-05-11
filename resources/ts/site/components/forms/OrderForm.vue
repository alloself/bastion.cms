<template>
    <form @submit.prevent="onSubmit">
        <fieldset class="border-t border-neutral-alpha pt-5 pb-10">
            <legend class="h2 float-left">Личная информация:</legend>
            <div class="clear-both"></div>
            <div class="lg:max-w-[50%]">
                <div class="app-form-field mb-4">
                    <input type="text" class="app-form-control" placeholder="Имя">
                </div>
                <div class="app-form-field mb-4">
                    <input type="text" class="app-form-control" placeholder="Фамилия">
                </div>
                <div class="app-form-field mb-4">
                    <input type="tel" class="app-form-control" placeholder="Телефон">
                </div>
                <div class="app-form-field mb-4">
                    <input type="email" class="app-form-control" placeholder="Эл. почта">
                </div>
                <div class="app-form-field mb-4">
                    <textarea class="app-form-control " placeholder="Комментарий"></textarea>
                </div>
            </div>
        </fieldset>

        <fieldset class="border-t border-neutral-alpha pt-5 pb-10">
            <legend class="h2 float-left">Способ доставки:</legend>
            <div class="clear-both"></div>
            <div class="flex flex-col items-start text-[16px] leading-[1.0] font-semibold lg:text-[20px]">
                <label class="flex items-center mb-2">
                    <div class="app-checkmark w-[22px] h-[22px] mr-3">
                        <input type="radio" checked name="delivery">
                        <div class="svg-icon">
                            <svg><use xlink:href="#radio"></use></svg>
                        </div>
                    </div>
                    <div>Самовывоз</div>
                </label>
                <label class="flex items-center mb-2">
                    <div class="app-checkmark w-[22px] h-[22px] mr-3">
                        <input type="radio" name="delivery">
                        <div class="svg-icon">
                            <svg><use xlink:href="#radio"></use></svg>
                        </div>
                    </div>
                    <div>Курьерская доставка</div>
                </label>
                <div class="mt-6">
                    <button
                        type="button"
                        class="app-button app-button--secondary"
                        v-modal-call="{name: 'pickUpPointsModal'}"
                    >Выбрать пункт самовывоза</button>
                </div>
            </div>
        </fieldset>

        <fieldset class="border-t border-neutral-alpha pt-5 pb-10">
            <legend class="h2 float-left">Способ оплаты:</legend>
            <div class="clear-both"></div>
            <div class="flex flex-col items-start text-[16px] leading-[1.0] font-semibold lg:text-[20px]">
                <label class="flex items-center mb-2">
                    <div class="app-checkmark w-[22px] h-[22px] mr-3">
                        <input type="radio" checked name="payment">
                        <div class="svg-icon">
                            <svg><use xlink:href="#radio"></use></svg>
                        </div>
                    </div>
                    <div>При получении (наличные или карта)</div>
                </label>
                <label class="flex items-center mb-2">
                    <div class="app-checkmark w-[22px] h-[22px] mr-3">
                        <input type="radio" name="payment">
                        <div class="svg-icon">
                            <svg><use xlink:href="#radio"></use></svg>
                        </div>
                    </div>
                    <div>Картой на сайте</div>
                </label>
                <div class="mt-6">
                    <button type="submit" class="app-button app-button--dark">Оформить заказ</button>
                </div>
                <label class="flex items-center mt-2 text-dark text-opacity-50 text-[10px]">
                    <div class="app-checkmark w-[22px] h-[22px] mr-3">
                        <input type="checkbox" checked name="agreement">
                        <div class="svg-icon">
                            <svg><use xlink:href="#checkmark"></use></svg>
                        </div>
                    </div>
                    <div>
                        Нажимая на кнопку «Отправить заказ» вы<br> соглашаетесь с условиями публичной оферты
                    </div>
                </label>
            </div>
        </fieldset>
    </form>
</template>

<script lang="ts" setup>
async function onSubmit() {}
</script>