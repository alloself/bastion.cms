import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from "url";
import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify';
import vueRouter from 'unplugin-vue-router/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/ts/admin/index.ts',
                'resources/ts/site/index.ts'
            ],
            refresh: true,
        }),
        vue({
            template: { transformAssetUrls },
        }),
        vuetify({
            styles: {
                configFile: 'resources/scss/admin/vuetify.scss',
            },
        }),
        vueRouter({
            dts: 'src/typed-router.d.ts',
          }),
    ],
    resolve: {
        alias: {
            "@": fileURLToPath(new URL("./src", import.meta.url)),
        },
        extensions: [".js", ".json", ".jsx", ".mjs", ".ts", ".tsx", ".vue"],
    },
    build: {
        sourcemap: true,
    },
    css: {
        preprocessorOptions: {
            sass: {
                api: 'modern-compiler',
            },
        },
    },
});