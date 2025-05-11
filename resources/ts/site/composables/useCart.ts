import { ref } from "vue";
import { useApi } from "./useApi";
import type { IBaseEntity } from "@admin/shared/types";


const cart = ref<IBaseEntity | null>(null)


export const useCart = () => {

    const { client } = useApi()
    const getCart = async (id: string) => {
        const { data } = await client.get(`/cart/${id}`)

        cart.value = data
    }

    const createCart = async () => {
        const { data } = await client.post('/cart')
        cart.value = data

        if (cart.value) {
            localStorage.setItem('cms:cart', cart.value.id)
        }

    }

    const addItemToCart = async () => {
        if (!cart.value) {
            await createCart()
        }
    }


    return {
        cart,
        getCart,
        createCart,
        addItemToCart
    }
}