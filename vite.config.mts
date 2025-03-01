import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { fileURLToPath, URL } from "url";
import vuetify, { transformAssetUrls } from "vite-plugin-vuetify";
import vueRouter from "unplugin-vue-router/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/ts/admin/index.ts",
        "resources/ts/site/index.ts",
        "resources/scss/site/index.scss",
        "resources/scss/admin/index.scss",
      ],
      refresh: true,
    }),
    vue({
      template: { transformAssetUrls },
    }),
    vuetify({
      styles: {
        configFile: "resources/scss/admin/vuetify.scss",
      },
    }),
    vueRouter({
      dts: "resources/types/typed-router.d.ts",
    }),
  ],
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./resources", import.meta.url)),
      "@admin": fileURLToPath(
        new URL("./resources/ts/admin", import.meta.url)
      )
    },
    extensions: [".js", ".json", ".jsx", ".mjs", ".ts", ".tsx", ".vue"],
  },
  build: {
    target: 'esnext',
    sourcemap: true,
    rollupOptions: {
      external: ['vuetify/components'],
    },
  },
  define: {
    'process.env': {},
  },
  worker: {
    format: 'es',
  },
  css: {
    preprocessorOptions: {
      sass: {
        api: "modern-compiler",
      },
    },
  },
  server: {
    host: '0.0.0.0', // Позволяет принимать подключения извне контейнера
    port: 5173,
    hmr: {
      host: 'localhost', // Или ваш домен
      protocol: 'ws',
      port: 5173,
    },
  },
});
