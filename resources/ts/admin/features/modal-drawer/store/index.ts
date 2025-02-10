
import { defineStore } from "pinia";
import { markRaw, ref, type Component } from "vue";

export interface IModal {
  component: Component;
  props: Record<string, unknown>;
  actions: Record<string, unknown>;
}

export const useModalDrawerStore = defineStore("modal-drawer", () => {
  const show = ref(false);

  const modals = ref<IModal[]>([]);

  const addModal = (modal: IModal) => {
    const component = markRaw(modal.component);
    modals.value.push({
      props: modal.props,
      actions: modal.actions,
      component,
    });
  };

  const onModalClose = () => {
    modals.value = modals.value.toSpliced(-1, 1);
  }

  return {
    modals,
    show,
    addModal,
    onModalClose
  };
});
