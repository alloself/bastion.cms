import { createApp } from "vue";
import App from '@admin/app/App.vue';
import { registerPlugins } from "@admin/shared/plugins";
import { useUserStore } from "@admin/entities/user/store";
import { client } from "@admin/shared/api/axios";

try {
  await client.get("/sanctum/csrf-cookie");

  const app = createApp(App);
  registerPlugins(app);
  const { getUser } = useUserStore();

  await getUser();

  app.mount("#admin");
} catch (e) {
  //location.href = "/"
}