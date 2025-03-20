import { Ref } from "vue";
import { useModalDrawerStore } from "../../features/modal-drawer";
import { client } from "../api/axios";
import { getModuleUrlPart, IModule } from "../modules";
import { IBaseEntity } from "../types";

interface IUseRelationMethodsArguments<T> {
    module: IModule;
    initialValues?: Record<string, unknown>;
}

export const useRelationMethods = <T extends IBaseEntity>({
    module,
    initialValues,
}: IUseRelationMethodsArguments<T>) => {
    const modalDrawerStore = useModalDrawerStore();

    const addRelation = (callback: (item: T) => void) => {
        modalDrawerStore.addDetailModal(
            {
                module,
                initialValues: initialValues ? { ...initialValues } : undefined,
            },
            {
                onCreate: (item: T) => {
                    callback(item);
                    modalDrawerStore.onModalClose();
                },
            }
        );
    };

    const editRelation = (id: string, callback: (item: T) => void) => {
        modalDrawerStore.addDetailModal(
            { module: module, id },
            {
                onUpdate: (item: T) => {
                    if (!item) {
                        return;
                    }
                    callback(item);
                },
            }
        );
    };

    const addExistingEntity = async (
        items: T[],
        callback: (items: T[]) => void,
        loading: Ref<boolean>
    ) => {
        try {
            loading.value = true;
            const results = await Promise.all(
                items.map(async (item: T) => {
                    const { data } = await client.patch(
                        `/api/admin/${getModuleUrlPart(module?.key)}/${
                            item.id
                        }`,
                        { ...item, ...initialValues }
                    );
                    return data as T;
                })
            );

            callback(results);
        } finally {
            loading.value = false;
        }
    };

    const deleteSelected = async (items: T[]) => {
        await Promise.all(
            items.map((item) =>
                client.patch(
                    `/api/admin/${getModuleUrlPart(module.key)}/${item.id}`,
                    {
                        ...item,
                        parent_id: null,
                    }
                )
            )
        );
    };

    return {
        addRelation,
        editRelation,
        addExistingEntity,
        deleteSelected,
    };
};
