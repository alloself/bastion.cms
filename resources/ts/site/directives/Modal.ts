import { modalToggle } from "@site/composables/useModal";
import type { DirectiveBinding } from "vue";

function clickHandler(binding: DirectiveBinding<{ name?: string }>, event: Event) {
    event.preventDefault();
    if (binding.value.name) {
        modalToggle(binding.value.name);
    }
}

export default {
    mounted(el: HTMLElement, binding: DirectiveBinding<{ name?: string }>) {
        el.addEventListener("click", clickHandler.bind(undefined, binding));
    },
    unmounted(el: HTMLElement, binding: DirectiveBinding<{ name?: string }>) {
        el.removeEventListener("click", clickHandler.bind(undefined, binding) as EventListener);
    },
};
