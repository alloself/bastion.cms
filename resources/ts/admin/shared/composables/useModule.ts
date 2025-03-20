import { computed } from "vue";
import { IModule, modules } from "../modules";

export const useModule = (key: string) => {
    const module = computed(() => modules.find((item) => item.key === key) as IModule);

    return {
        module,
    };
};
