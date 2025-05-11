function clickHandler(params: Record<string, any>, event: Event) {
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
    mounted(el: HTMLElement, binding: any) {
        el.addEventListener('click', clickHandler.bind(undefined, binding.value))
    },
    unmounted(el: HTMLElement, binding: any) {
        el.removeEventListener('click', clickHandler.bind(undefined, binding.value))
    }
}