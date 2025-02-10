import { defineStore } from "pinia";
import { ref, type Component } from "vue";

export interface IScreen {
  tabs: Array<Component | string>,
  id?: Symbol
}

const emptyScreen = {
  tabs: []
}

export const useScreenStore = defineStore("screen", () => {

  const screens = ref<IScreen[]>([
    {
      tabs: []
    }
  ])


  const addScreen = () => {
    screens.value.push({
      id: Symbol('id'),
      ...Object.assign(emptyScreen)
    })
  }

  const removeScreen = (index: number) => {
    screens.value.splice(index, 1);
  }

  const addTab = (screenIndex: number, component: Component | string) => {
    screens.value[screenIndex].tabs.push(component)
  }

  const removeTab = (screenIndex: number, index: number) => {
    screens.value[screenIndex].tabs.splice(index, 1)
  }


  return {
    screens,
    addScreen,
    removeScreen,
    addTab,
    removeTab
  }
});