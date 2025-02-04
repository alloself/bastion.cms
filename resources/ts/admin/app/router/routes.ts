//import { useAppModules } from "@/shared/composables/useAppModules";
import { type RouteRecordRaw } from "vue-router";

//const { createRoutes } = useAppModules();

export const routes: RouteRecordRaw[] = [
    {
        path: "/",
        name: "Dashboard",
        component: () => import("@admin/pages/Dashboard.vue"),
       children: [
            /* ...createRoutes(import.meta.env.VITE_APP_MODULES.split(",")), */
            {
                path: "",
                name: "test",
                component: () => import("@admin/pages/test.vue"),
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
    }
];
