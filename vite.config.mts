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
      refresh: false,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
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
      ),
      "@site": fileURLToPath(
        new URL("./resources/ts/site", import.meta.url)
      ),
    },
    extensions: [".js", ".json", ".jsx", ".mjs", ".ts", ".tsx", ".vue"],
  },
  build: {
    target: "esnext",
    sourcemap: false,
    minify: "terser",
    cssCodeSplit: true,
    assetsInlineLimit: 4096,
    emptyOutDir: true,
    reportCompressedSize: false,
  },
  define: {
    "process.env": {},
  },
  worker: {
    format: "es",
  },
  css: {
    preprocessorOptions: {
      scss: {
        quietDeps: true,
        silenceDeprecations: ["legacy-js-api"],
      },
    },
  },
  server: {
    host: "0.0.0.0",
    port: 5173,
    fs: { allow: ["."] },
    watch: {
      usePolling: true,
      ignored: ["**/storage/framework/views/**"],
    },
    hmr: {
      host: "localhost",
      protocol: "ws",
      port: 5173,
    },
  },
});
