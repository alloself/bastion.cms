import { type RouteRecordRaw } from "vue-router";
import { modules, createCRUDModulesRoutes } from "@admin/shared/modules";

export const routes: RouteRecordRaw[] = [
    {
        path: "/",
        name: "Dashboard",
        component: () => import("@admin/pages/Dashboard.vue"),
        children: [
            ...createCRUDModulesRoutes(modules),
            {
                path: "/import",
                name: "Import",
                component: () => import("@admin/pages/Import.vue"),
            },
        ],
    },
    {
        path: "/login",
        name: "Login",
        component: () => import("@admin/pages/Login.vue"),
    },
    {
        path: "/:pathMatch(.*)*",
        name: "404",
        redirect: "/login",
    },
];
