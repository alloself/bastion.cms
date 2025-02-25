import { computed } from "vue";
import { modules } from "../modules";

export const useModule = (key: string) => {
    const module = computed(() => modules.find((item) => item.key === key));

    return {
        module,
    };
};
