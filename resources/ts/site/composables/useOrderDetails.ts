import { ref } from 'vue'

export const orderID = ref<string | null>(null)

export const setOrderID = (id: string) => {
  orderID.value = id;
};

export default {
  orderID,
  setOrderID
};