import { ContentBlock } from "@/ts/types/models";
import link from "@admin/shared/forms/link";
import type {
    IOptionsFieldsFabric,
    ISmartFormField,
} from "@admin/shared/types";
import { computed, markRaw, defineAsyncComponent } from "vue";

export const createFields = (options?: IOptionsFieldsFabric<ContentBlock>) => {
    const WYSIWYGEditor = defineAsyncComponent(
        () => import("@admin/shared/components/WYSIWYGEditor.vue")
    );

    const RelationAutocomplete = defineAsyncComponent(
        () => import("@admin/shared/components/RelationAutocomplete.vue")
    );
    const RelationTree = defineAsyncComponent(
        () => import("@admin/shared/components/RelationTree.vue")
    );

    const fields = computed<ISmartFormField[]>(() => [
        {
            component: "v-text-field",
            key: "name",
            props: {
                autocomplete: "name",
                label: "Название",
                name: "name",
                type: "text",
            },
        },
        ...link,
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
        {
            component: markRaw(RelationAutocomplete),
            key: "parent_id",
            props: {
                autocomplete: "parent_id",
                label: "Родительский блок",
                name: "parent_id",
                itemValue: "id",
                itemTitle: "name",
                moduleKey: "contentBlock",
                initialItems: options?.entity?.parent
                    ? [options?.entity?.parent]
                    : [],
            },
        },
        {
          component: markRaw(WYSIWYGEditor),
          key: "content",
        },
    ]);

    if (options?.entity?.id) {
        fields.value.push({
            component: markRaw(RelationTree),
            key: "children",
            props: {
                initialValues: { parent_id: options.entity.id },
                moduleKey: "contentBlock",
                itemTitle: "name",
            },
        });
    }

    return {
        fields,
    };
};
