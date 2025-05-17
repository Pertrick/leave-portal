import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useFlash() {
    const { props } = usePage()
    const flash = ref(props.flash)

    onMounted(() => {
        // Clear flash messages after they are shown
        if (flash.value) {
            setTimeout(() => {
                flash.value = null
            }, 5000)
        }
    })

    return {
        flash
    }
} 