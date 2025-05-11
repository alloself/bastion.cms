import { computed, ref } from 'vue'

export const offcanvasVisibleState = ref<Record<string, boolean>>({})

export const offcanvasVisibleAvailable = computed<boolean>(() => {
    return Object.values(offcanvasVisibleState.value).includes(true)
})

export function offcanvasToggle(offcanvasName: string) {
    if (offcanvasVisibleState.value[offcanvasName] !== undefined) {
        offcanvasVisibleState.value[offcanvasName] = !offcanvasVisibleState.value[offcanvasName]
        document.body.classList[offcanvasVisibleState.value[offcanvasName] === true ? 'add' : 'remove']('is-offcanvas-open')
    }
}

export function offcanvasClose(offcanvasName?: string) {
    if (offcanvasName && offcanvasVisibleState.value[offcanvasName] !== undefined) {
        offcanvasVisibleState.value[offcanvasName] = false
        return
    }

    for (const state in offcanvasVisibleState.value) {
        offcanvasVisibleState.value[state] = false
    }

    document.body.classList.remove('is-offcanvas-open')
}