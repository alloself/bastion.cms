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
    optimizeDeps: {
        include: [
            'vue',
            'vue/dist/vue.esm-bundler.js',
            'axios'
        ],
        exclude: [
            'three', 
            '@yandex/maps',
            'swiper',
            'baguettebox.js'
        ]
    },
    build: {
        target: "esnext",
        sourcemap: false,
        minify: "terser",
        cssCodeSplit: true,
        assetsInlineLimit: 4096,
        emptyOutDir: true,
        reportCompressedSize: false,
        chunkSizeWarningLimit: 500,
        rollupOptions: {
            output: {
                manualChunks: {
                    // Критический Vue код
                    'vue-core': ['vue', 'vue/dist/vue.esm-bundler.js'],
                    
                    // Критические UI компоненты (загружаются сразу)
                    'ui-critical': [
                        './resources/ts/site/components/common/Details.vue',
                        './resources/ts/site/components/common/AppSearch.vue',
                        './resources/ts/site/components/common/MobileMenuBurger.vue',
                        './resources/ts/site/components/common/Offcanvas.vue',
                        './resources/ts/site/components/common/OffcanvasClose.vue'
                    ],
                    
                    // Компоненты навигации и базовые элементы
                    'ui-navigation': [
                        './resources/ts/site/components/common/Accordion.vue',
                        './resources/ts/site/components/common/AppTabs.vue',
                        './resources/ts/site/components/common/Pagination.vue',
                        './resources/ts/site/components/common/AppSort.vue',
                        './resources/ts/site/components/common/AppSelect.vue'
                    ],
                    
                    // Формы (ленивая загрузка)
                    'forms-auth': [
                        './resources/ts/site/components/forms/LoginForm.vue',
                        './resources/ts/site/components/forms/RegistrationForm.vue',
                        './resources/ts/site/components/forms/PasswordRecoveryForm.vue'
                    ],
                    
                    'forms-order': [
                        './resources/ts/site/components/forms/OrderForm.vue',
                        './resources/ts/site/components/forms/PersonalDataForm.vue',
                        './resources/ts/site/components/forms/PersonalPasswordForm.vue',
                        './resources/ts/site/components/forms/CallbackForm.vue'
                    ],
                    
                    // Геолокация и карты
                    'geo-components': [
                        './resources/ts/site/components/common/GeoLocation.vue',
                        './resources/ts/site/components/common/GeoLocationList.vue',
                        './resources/ts/site/components/common/PickUpPoints.vue'
                    ],
                    
                    // Каталог и фильтры
                    'catalog-components': [
                        './resources/ts/site/components/common/CatalogFilter.vue',
                        './resources/ts/site/components/common/MobileFiltersBurger.vue',
                        './resources/ts/site/components/common/CountButtons.vue'
                    ],
                    
                    // Модальные окна
                    'modal-components': [
                        './resources/ts/site/components/common/AppModal.vue'
                    ],
                    
                    // Слайдеры (ленивая загрузка)
                    'sliders': [
                        'swiper'
                    ],
                    
                    // Галереи (ленивая загрузка)
                    'gallery': [
                        'baguettebox.js'
                    ],
                    
                    // Three.js (самая ленивая загрузка)
                    'three-js': [
                        'three'
                    ],
                    
                    // Скрипты инициализации (ленивая загрузка)
                    'init-scripts': [
                        './resources/ts/site/scripts/initSliders.ts',
                        './resources/ts/site/scripts/initGallery.ts',
                        './resources/ts/site/scripts/initScrollAnimate.ts'
                    ],
                    
                    // Тяжелые скрипты (самая ленивая загрузка)
                    'heavy-scripts': [
                        './resources/ts/site/scripts/initThreeObjects.ts',
                        './resources/ts/site/scripts/initYandexMap.ts',
                        './resources/ts/site/scripts/threeLampScene.js'
                    ],
                    
                    // Утилиты
                    'utils': [
                        './resources/ts/site/utils/workClocksNightMode.ts'
                    ],
                    
                    // Composables
                    'composables': [
                        './resources/ts/site/composables/useLiked.ts',
                        './resources/ts/site/composables/useCart.ts'
                    ]
                },
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: (chunkInfo) => {
                    // Критические чанки получают приоритетные имена
                    if (chunkInfo.name?.includes('vue-core') || chunkInfo.name?.includes('ui-critical')) {
                        return 'assets/critical/[name]-[hash].js';
                    }
                    // Ленивые чанки в отдельную папку
                    if (chunkInfo.name?.includes('heavy') || chunkInfo.name?.includes('three')) {
                        return 'assets/lazy/[name]-[hash].js';
                    }
                    return 'assets/[name]-[hash].js';
                },
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/\.(woff2?|eot|ttf|otf)(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/fonts/[name]-[hash].[ext]';
                    }
                    if (/\.(png|jpe?g|gif|svg|webp)(\?.*)?$/i.test(assetInfo.name)) {
                        return 'assets/images/[name]-[hash].[ext]';
                    }
                    return `assets/[name]-[hash].[ext]`;
                }
            }
        },
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.info', 'console.debug', 'console.warn'],
                passes: 3,
                unsafe: true,
                unsafe_comps: true,
                unsafe_math: true,
                unsafe_methods: true,
                unsafe_proto: true,
                unsafe_regexp: true,
                unsafe_undefined: true
            },
            mangle: {
                safari10: true,
                toplevel: true
            },
            format: {
                comments: false
            }
        }
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
        devSourcemap: false
    },
    server: {
        warmup: {
            clientFiles: [
                './resources/ts/site/index.ts',
                './resources/ts/site/components/common/Details.vue',
                './resources/ts/site/components/common/AppSearch.vue',
                './resources/ts/site/components/common/MobileMenuBurger.vue'
            ]
        },
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
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            if (hostType === 'js') {
                return { js: `/${filename}` };
            }
            return { relative: true };
        }
    }
});
