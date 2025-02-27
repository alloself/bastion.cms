import type { Page } from "@/ts/types/models";
import link from "@admin/shared/forms/link";
import type {
    IOptionsFieldsFabric,
    ISmartFormField,
} from "@admin/shared/types";
import { computed, markRaw, defineAsyncComponent } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<Page>) => {
    const RelationAutocomplete = defineAsyncComponent(
        () => import("@admin/shared/components/RelationAutocomplete.vue")
    );
    const RelationTree = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTree.vue")
    );

    const JSONEditor = defineAsyncComponent(
        () => import("@admin/shared/components/JSONEditor.vue")
    );

    const fields = computed<ISmartFormField[]>(() => [
        ...link,
        {
            component: markRaw(JSONEditor),
            key: "meta",
            props: {
                title: "Мета",
            },
        },
        {
            component: "v-checkbox",
            key: "index",
            props: {
                autocomplete: "index",
                label: "Главная страница",
                name: "index",
            },
        },
        {
            component: markRaw(RelationAutocomplete),
            key: "template_id",
            props: {
                autocomplete: "template_id",
                label: "Шаблон",
                name: "template_id",
                itemValue: "id",
                itemTitle: "name",
                moduleKey: "template",
                initialItems: options?.entity?.template
                    ? [options?.entity?.template]
                    : [],
            },
        },
    ]);
    
    if (options?.entity?.id) {
        fields.value.push({
            component: markRaw(RelationTree),
            key: "children",
            props: {
                initialValues: { parent_id: options.entity.id },
                moduleKey: "page",
                orderable: false,
                itemTitle: "link.title",
            },
        });
    }
    return {
        fields,
    };
};
