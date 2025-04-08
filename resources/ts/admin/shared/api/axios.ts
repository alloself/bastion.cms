import router from "@admin/app/router";
import { useUserStore } from "@admin/entities/user";
import axios from "axios";
import { ref } from "vue";
import { useNotificationsStore } from "@admin/features/notifications";

export const loading = ref(false);

function cleanEmptyValues<T>(data: T): T | undefined {
    if (Array.isArray(data)) {
        const cleanedArray = data
            .map(cleanEmptyValues)
            .filter((item) => item !== undefined);

        return cleanedArray.length ? (cleanedArray as unknown as T) : undefined;
    }

    if (data && typeof data === "object") {
        const cleanedObject = Object.entries(data).reduce(
            (acc, [key, value]) => {
                const cleanedValue = cleanEmptyValues(value);

                if (cleanedValue !== undefined) {
                    acc[key] = cleanedValue;
                }

                return acc;
            },
            {} as Record<string, unknown>
        );

        return Object.keys(cleanedObject).length
            ? (cleanedObject as T)
            : undefined;
    }

    // Проверка примитивов на пустоту
    if (data === null || data === undefined || data === "") {
        return undefined;
    }

    return data;
}

function hasFile(data: Record<string, any>): boolean {
    return Object.values(data).some((value) =>
      value instanceof File ||
      value instanceof Blob ||
      (Array.isArray(value) && value.some((item) => item instanceof File || item instanceof Blob))
    );
  }

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
        console.log(config.data);
        if (config.data && !hasFile(config.data)) {
            config.data = cleanEmptyValues(config.data);
        }
        if (config.data instanceof Object && hasFile(config.data)) {
            const formData = new FormData();
            console.log(formData)

            Object.entries(config.data).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach((val) => formData.append(key, val));
                } else {
                    formData.append(key, value as any);
                }
            });

            config.data = formData;

            if (config.headers) {
                delete config.headers["Content-Type"];
            }
        }
        loading.value = true;
        console.log(config.data);
        return config;
    },
    (error) => {
        return error;
    }
);

client.interceptors.response.use(
    (config) => {
        loading.value = false;
        return config;
    },
    async (error) => {
        loading.value = false;
        const errorMessage = error?.response?.data?.message || error.message;
        const userStore = useUserStore();

        if (error.response?.status === 401 || error.response?.status === 403) {
            if (userStore.user) {
                await userStore.logout();
            }
            router.push({ name: "Login" });
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
