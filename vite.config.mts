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
    sourcemap: false,
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
        pure_funcs: ['console.log', 'console.info'],
      },
    },
    rollupOptions: {
      external: ['vuetify/components'],
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router'],
          vuetify: ['vuetify'],
        },
        chunkFileNames: 'assets/js/[name]-[hash].js',
        entryFileNames: 'assets/js/[name]-[hash].js',
        assetFileNames: 'assets/[ext]/[name]-[hash].[ext]',
      },
    },
    cssCodeSplit: true,
    assetsInlineLimit: 4096,
    emptyOutDir: true,
    reportCompressedSize: false,
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
    host: '0.0.0.0', 
    port: 5173,
    hmr: {
      host: 'localhost',
      protocol: 'ws',
      port: 5173,
    },
  },
});
