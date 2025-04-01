import { IScreen } from "@/ts/admin/shared/types";
import { defineStore } from "pinia";
import { computed, ref, watch, type Component } from "vue";
import { RouteLocationNormalizedLoaded, useRoute, useRouter } from "vue-router";

const STORAGE_KEY = 'screens-state'


function loadScreens(): IScreen[] {
  const stored = localStorage.getItem(STORAGE_KEY)
  return stored
    ? JSON.parse(stored)
    : [{ id: crypto.randomUUID(), tabs: [], activeTabId: '' }]
}


export const useScreenStore = defineStore("screen", () => {

  const router = useRouter()
  const route = useRoute()

  const screens = ref<IScreen[]>(loadScreens())

  function generateTabTitle(route: RouteLocationNormalizedLoaded): string {
    return (route.meta.title || route.name || route.fullPath) as string
  }

  // Открыть вкладку по текущему route
  function openTab(route: RouteLocationNormalizedLoaded) {
    const screen = screens.value[0] // Используем один экран (можно расширить)

    // Проверка существования вкладки
    let tab = screen.tabs.find(t => t.fullPath === route.fullPath)
    if (!tab) {
      tab = {
        id: crypto.randomUUID(),
        title: generateTabTitle(route),
        fullPath: route.fullPath,
        routeName: route.name as string,
        params: route.params,
        query: route.query
      }
      screen.tabs.push(tab)
    }
    screen.activeTabId = tab.id
  }

  // Закрыть вкладку
  function closeTab(tabId: string) {
    const screen = screens.value[0]
    const tabIndex = screen.tabs.findIndex(t => t.id === tabId)
    if (tabIndex === -1) return

    const wasActive = screen.activeTabId === tabId
    screen.tabs.splice(tabIndex, 1)

    if (wasActive) {
      const nextTab = screen.tabs[tabIndex] || screen.tabs[tabIndex - 1]
      if (nextTab) {
        activateTab(nextTab.id)
      } else {
        screen.activeTabId = ''
        router.push('/')
      }
    }
  }

  function activateTab(tabId: string) {
    const screen = screens.value[0]
    const tab = screen.tabs.find(t => t.id === tabId)
    if (tab) {
      screen.activeTabId = tabId
      router.push(tab.fullPath)
    }
  }

  function removeScreen(screenId: string): void {
    const index = screens.value.findIndex(s => s.id === screenId)
    if (index !== -1) {
      screens.value.splice(index, 1)
    }
  }

  watch(screens, (newValue) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newValue))
  }, { deep: true })

  watch(() => route.fullPath, () => {
    openTab(route)
  }, { immediate: true })

  return {
    screens: computed(() => screens.value),
    openTab,
    closeTab,
    activateTab,
    removeScreen
  }
});