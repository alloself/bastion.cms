import { ref } from 'vue'

export const modalVisibleState = ref<Record<string, boolean>>({})

export function modalToggle(modalName: string) {
    if (modalName && modalVisibleState.value[modalName] !== undefined) {
        modalVisibleState.value[modalName] = !modalVisibleState.value[modalName]
        document.body.classList[modalVisibleState.value[modalName] === true ? 'add' : 'remove']('is-modal-open')
    }
}

export function modalClose(modalName?: string) {
    if (modalName && modalVisibleState.value[modalName] !== undefined) {
        modalVisibleState.value[modalName] = false
    }
    document.body.classList.remove('is-modal-open')
}