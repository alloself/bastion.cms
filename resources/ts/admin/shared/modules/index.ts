import type { RouteRecordRaw } from "vue-router";
import { capitalize } from "lodash";
import { type RouteLocation } from "vue-router";

export interface IModule {
  key: string;
  title: string;
  icon?: string;
  showInNavigation?: boolean;
  headers: Array<{ title: string; key: string }>;
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
        key: "link.path",
      },
      {
        title: "Язык",
        key: "language.title",
      },
    ],
  },
];

export const createCRUDModulesRoutes = (array: IModule[]): RouteRecordRaw[] => {
  return array.reduce((acc, item) => {
    const routes: RouteRecordRaw[] = [];

    if (item.showInNavigation) {
      routes.push({
        path: `/${item.key}`,
        name: `${capitalize(item.key)}List`,
        alias: item.key === 'page' ? '/' : undefined,
        props: {
          module: item,
        },
        component: () =>
          import(`@admin/shared/modules/components/List.vue`),
      } as RouteRecordRaw);
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

    acc.push(...routes)

    return acc;
  }, [] as RouteRecordRaw[]);
};
