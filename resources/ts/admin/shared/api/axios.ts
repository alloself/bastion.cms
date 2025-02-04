
import router from "@admin/app/router";
import { useUserStore } from "@admin/entities/user/store";
import axios from "axios";
import { ref } from "vue";
import { useNotificationsStore } from "@admin/features/notifications/store/notificationsStore";


export const loading = ref(false)

export const client = axios.create({
  baseURL: `/`,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: "application/json",
  },
});

client.interceptors.request.use(
  (config) => {
    loading.value = true
    return config;
  },
  (error) => {
    return error
  }
);

client.interceptors.response.use(
  (config) => {
    loading.value = false
    return config;
  },
  async (error) => {
    loading.value = false
    const errorMessage = error?.response?.data?.message || error.message;
    const userStore = useUserStore();

    if (error.response?.status === 401 || error.response?.status === 403) {
      if (userStore.user) {
        await userStore.logout();
      }
      router.push({ name: 'Login' })
      return;
    }

    const notificationsStore = useNotificationsStore();

    notificationsStore.pushNotification({
      content: errorMessage,
      color: "error",
    });


    return Promise.reject(error);
  }
);
