import { client, loading } from "../api/axios";
import { getModuleUrlPart, IModule } from "../modules";
import { IBaseEntity, IOrderedEntity } from "../types";

export const useOrderedEntity = <T extends IOrderedEntity & IBaseEntity>(
    module: IModule
) => {
    const updateOrder = async (
        item: T,
        value: string | number,
        morph = false
    ) => {
        if (morph) {
            if (!item.pivot) {
                item.pivot = {
                    order: 0,
                };
            }
            item.pivot.order = Number(value);
            return item;
        } else {
            loading.value = true;
            try {
                const { data } = await client.patch(
                    `/api/admin/${getModuleUrlPart(module.key)}/${item.id}`,
                    {
                        ...item,
                        order: value,
                    }
                );

                return data;
            } finally {
                loading.value = true;
            }
        }
    };

    const getItemOrder = (item: T, morph = false) => {
        if (morph) {
            return item?.pivot?.order || 0;
        }
        return item.order;
    };

    return {
        updateOrder,
        getItemOrder,
    };
};
