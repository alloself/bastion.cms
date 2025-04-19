<div class="order-details lg:px-16 lg:py-12">
    <div class="h2 mb-10" v-if="state.orderDetails?.id">
        Состав заказа № 1655164
    </div>
    <div class="orders-items-list space-y-10">
        @for ($i = 0; $i < 4; $i++)
            <x-order-item></x-order-item>
        @endfor
    </div>
    <div class="mt-10 flex items-center justify-between leading-[1.1] font-semibold md:justify-end">
        <div class="text-[14px] text-dark text-opacity-40 md:mr-[110px]">Товары (4):</div>
        <div class="text-right">
            <div class="text-[28px] mb-2">34 328 ₽</div>
            <div class="text-[20px] text-dark text-opacity-40 line-through">3 990 ₽</div>
        </div>
    </div>
</div>