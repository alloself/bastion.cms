import vuetify from '@admin/shared/plugins/vuetify';
import { createPinia } from 'pinia';
import type { App } from 'vue'
import router from '@admin/app/router';

const pinia = createPinia();

export function registerPlugins(app: App) {
  app
    .use(vuetify)
    .use(router)
    .use(pinia)
}