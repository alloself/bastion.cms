import { ref } from "vue";





const liked = ref<string[]>([])


export const useLiked = () => {

    function getLikedIds() {
        const arrayStr = localStorage.getItem('cms:liked')

        if (!arrayStr) {
            return []
        }


        return JSON.parse(arrayStr)
    }

    function toggleLiked(id: string) {
        const old = localStorage.getItem('cms:liked');

        let parsedOld = [] as string[]
        if (old) {
            parsedOld = JSON.parse(old) as string[]
        }

        const index = parsedOld.findIndex((item) => item === id)

        if (index === -1) {
            const value = [...parsedOld, id] as string[]
            liked.value = value
            localStorage.setItem('cms:liked', JSON.stringify(value))
        }
        else {
            const value = parsedOld.toSpliced(index, 1) as string[]
            liked.value = value
            localStorage.setItem('cms:liked', JSON.stringify(value))
        }
    }


    return {
        toggleLiked,
        liked,
        getLikedIds
    }
}