
import { defineStore } from "pinia";
import { ref } from "vue";

export const useNavigationDrawerStore = defineStore("navigation-drawer", () => {

  const showNavigationDrawer = ref(false)

  const toggle = () => {
    showNavigationDrawer.value = !showNavigationDrawer.value
  }

  return {
    showNavigationDrawer,
    toggle
  };
});
