import type { RouteRecordRaw } from "vue-router";
import { capitalize } from "lodash";
import { type RouteLocation } from "vue-router";
import type { IRelationFieldConfig, ISmartFormField } from "../types";
import { defineAsyncComponent, type Component } from "vue";

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
        key: "link.path",
      },
      {
        title: "Язык",
        key: "language.title",
      },
    ],
    relations: []
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
        key: "middle_name",
      },
      {
        title: "Отчество",
        key: "last_name",
      },
    ],
    relations: ['audits']
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

type ComponentCache = Record<string, Component>

const componentCache: ComponentCache = {}

const getAsyncComponent = async (loader: () => Promise<Component>): Promise<Component> => {
  const cacheKey = loader.toString()
  if (!componentCache[cacheKey]) {
    componentCache[cacheKey] = defineAsyncComponent(loader)
  }
  return componentCache[cacheKey]
}

export const formComponents: ComponentCache = {
  //'relation-table': () => getAsyncComponent(() => import("@/shared/components/MultipleRelationTable.vue")),
  //'relation-tree': () => getAsyncComponent(() => import("@/shared/components/MultipleRelationTree.vue")),
  //'file': () => getAsyncComponent(() => import("@/shared/components/FilesTable.vue")),
  //'autocomplete': () => getAsyncComponent(() => import("@/shared/components/SingleRelationAutocomplete.vue")),
  //'json-editor': () => getAsyncComponent(() => import("@/shared/components/JSONEditor.vue"))
}


export const createField = {
  // Базовое текстовое поле
  text: (key: string, label: string): ISmartFormField => ({
    component: "v-text-field",
    key,
    props: { label, name: key, type: "text" }
  }),

  /*
  relation: async (key: string, config: IRelationFieldConfig): Promise<ISmartFormField> => {
    const component = await formComponents[config.type]()

    return {
      component,
      key,
      props: {
        title: config.title || key,
        moduleKey: config.moduleKey || key,
        morphRelation: config.morphRelation,
        propHeaders: config.propHeaders,
        initialValues: config.initialValues,
        itemValue: config.itemValue,
        itemTitle: config.itemTitle,
        type: config.fileType
      }
    }
  },*/
}