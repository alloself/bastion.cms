export function initScrollAnimate() {
    const observer = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-inview')
                } else {
                    entry.target.classList.remove('is-inview')
                }
            })
        },
        {
            root: null,
            rootMargin: '0px',
            threshold: 0.2,
        }
    )

    document.querySelectorAll('[data-animated-block]').forEach((el) => {
        observer.observe(el)
    })
}
