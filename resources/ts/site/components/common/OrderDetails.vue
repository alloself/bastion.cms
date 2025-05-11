<template>
    <div class="order-details lg:px-16 lg:py-12">
        <div
            v-if="state.loading"
            class="flex items-center justify-center min-h-[220px]"
        >
            <div class="svg-icon animate-spin w-14 h-14 text-brand">
                <svg><use xlink:href="#spinner"></use></svg>
            </div>
        </div>
        <template v-else>
            <div class="h2 mb-10" v-if="state.orderDetails?.id">
                Состав заказа №{{ state.orderDetails.id }}:
            </div>
            <div class="orders-items-list space-y-10">
                <OrderItem
                    v-for="order in 4"
                    :key="order"
                    placement="modal"
                ></OrderItem>
            </div>
            <div
                class="mt-10 flex items-center justify-between leading-[1.1] font-semibold md:justify-end"
            >
                <div
                    class="text-[14px] text-dark text-opacity-40 md:mr-[110px]"
                >
                    Товары (4):
                </div>
                <div class="text-right">
                    <div class="text-[28px] mb-2">34 328 ₽</div>
                    <div
                        class="text-[20px] text-dark text-opacity-40 line-through"
                    >
                        3 990 ₽
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup lang="ts">
import { reactive, watch } from "vue";
import OrderItem from "@site/components/blocks/OrderItem.vue";
import { orderID } from "@site/composables/useOrderDetails";

const state = reactive<{
    loading: boolean;
    orderDetails: Record<string, any>;
}>({
    loading: false,
    orderDetails: {},
});

async function loadOrderDetails() {
    setTimeout(() => {
        state.loading = false;
        state.orderDetails.id = Date.now();
        orderID.value = null;
    }, 500);
}

watch(orderID, () => {
    if (orderID.value !== null) {
        state.loading = true;
        loadOrderDetails();
    }
});
</script>
