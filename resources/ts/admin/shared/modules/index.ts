import type { RouteRecordRaw } from "vue-router";
import { capitalize } from "lodash";
import { type RouteLocation } from "vue-router";
import Detail from "@admin/shared/modules/components/Detail.vue";
import { createProvide } from "../helpers";

export interface IModule {
    key: string;
    title: string;
    icon?: string;
    showInNavigation?: boolean;
    headers: Array<{ title: string; key: string }>;
    relations?: string[];
}

export const modules: IModule[] = [
    {
        key: "page",
        title: "Страницы",
        icon: "mdi-file",
        showInNavigation: true,
        headers: [
            {
                title: "Заголовок",
                key: "link.title",
            },
            {
                title: "Ссылка",
                key: "link.url",
            },
            {
                title: "Язык",
                key: "language.title",
            },
        ],
        relations: [
            "link",
            "audits.user",
            "children.link",
            "contentBlocks",
            "attributes",
        ],
    },
    {
        key: "user",
        title: "Пользователи",
        icon: "mdi-account-group",
        showInNavigation: true,
        headers: [
            {
                title: "Email",
                key: "email",
            },
            {
                title: "Имя",
                key: "first_name",
            },
            {
                title: "Фамилия",
                key: "last_name",
            },
            {
                title: "Отчество",
                key: "middle_name",
            },
        ],
        relations: ["audits.user"],
    },
    {
        key: "template",
        title: "Шаблоны",
        icon: "mdi-code-greater-than-or-equal",
        showInNavigation: true,
        headers: [
            {
                title: "Название",
                key: "name",
            },
        ],
        relations: ["audits.user"],
    },
    {
        key: "contentBlock",
        title: "Блоки",
        icon: "mdi-toy-brick",
        showInNavigation: true,
        headers: [
            {
                title: "Название",
                key: "name",
            },
        ],
        relations: ["audits.user", "children", "link"],
    },
    {
        key: "attribute",
        title: "Аттрибуты",
        icon: "mdi-attachment",
        showInNavigation: true,
        headers: [
            {
                title: "Название",
                key: "name",
            },
            {
                title: "Ключ",
                key: "key",
            },
        ],
        relations: ["audits.user"],
    },
];

export const createCRUDModulesRoutes = (array: IModule[]): RouteRecordRaw[] => {
    return array.reduce((acc, item) => {
        const routes: RouteRecordRaw[] = [];

        if (item.showInNavigation) {
            const listRoute = {
                path: `/${item.key}`,
                name: `${capitalize(item.key)}List`,
                props: {
                    module: item,
                },
                component: () =>
                    import(`@admin/shared/modules/components/List.vue`),
            } as RouteRecordRaw;
            if (item.key === "page") {
                listRoute.alias = "/";
            }
            routes.push(listRoute);
        }
        routes.push(
            {
                path: `/${item.key}/create`,
                name: `${capitalize(item.key)}Create`,
                props: {
                    module: item,
                },
                component: () =>
                    import(`@admin/shared/modules/components/Detail.vue`),
            },
            {
                path: `/${item.key}/:id`,
                name: `${capitalize(item.key)}Detail`,
                props: (route: RouteLocation) => ({
                    id: route.params.id,
                    module: item,
                }),
                component: () =>
                    import(`@admin/shared/modules/components/Detail.vue`),
            }
        );

        acc.push(...routes);

        return acc;
    }, [] as RouteRecordRaw[]);
};

export { Detail };

export const getModuleUrlPart = (key: string) => {
    return key
        .replace(/([A-Z])/g, (letter) => `-${letter.toLowerCase()}`)
        .replace(/^-/, "")
        .toLowerCase();
};
