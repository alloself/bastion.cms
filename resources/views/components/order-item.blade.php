<div class="order-item flex">
    <div class="relative text-center mr-5 w-[72px] md:w-[200px] md:mb-0 group">
        <a href="#" class="absolute inset-0 z-[5]"></a>
        <img class="transition-all duration-300 object-cover inline-block group-hover:drop-shadow-lg" src="/public/img/cart-pic-1.png" alt="">
    </div>
    <div class="flex-1 flex flex-col items-start min-w-0 border-b border-neutral [&.is-active-link]:text-brand pb-5">
        <div class="mb-5 lg:flex lg:w-full">
            <div>
                <div class="text-[14px] font-semibold leading-[1.0] md:text-[28px]">
                    Заказ № 1614684
                </div>
                <div class="mt-2.5 text-[10px] font-medium text-dark text-opacity-40 md:text-[14px]">доставлен 25 сентября 2023</div>
            </div>
            <div class="text-[14px] mt-4 font-semibold leading-[1.2] md:text-[20px] lg:flex-none lg:text-right lg:ml-auto lg:mt-0">
                <div>990 ₽</div>
                <div class="text-dark text-opacity-40 line-through">3 990 ₽</div>
            </div>
        </div>
        <div
            v-modal-call="{name: 'orderDetailsModal', orderID: Date.now()}"
            class="cursor-pointer text-dark text-opacity-50 border-b border-dark border-opacity-50 text-[14px] font-semibold leading-[1.1] mt-auto md:text-[20px]"
        >Состав заказа</div>
        <div 
            class="text-[20px] font-semibold leading-[1.2] mt-auto"
        >4 шт</div>
    </div>
</div>