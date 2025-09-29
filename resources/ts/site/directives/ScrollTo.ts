function clickHandler(params: { target: string; offsetY?: number }, event: Event) {
    event.preventDefault()
    let target = document.querySelector(params.target) as HTMLElement
    let offset = params.offsetY || 0
    let scrollValue = +(target.getBoundingClientRect().top + window.scrollY - offset)

    if (target) {
        window.scrollTo({
            top: scrollValue,
            behavior: "smooth",
        });
    }
}

export default {
    mounted(el: HTMLElement, binding: { value: { target: string; offsetY?: number } }) {
        el.addEventListener('click', clickHandler.bind(undefined, binding.value))
    },
    unmounted(el: HTMLElement, binding: { value: { target: string; offsetY?: number } }) {
        el.removeEventListener('click', clickHandler.bind(undefined, binding.value))
    }
}