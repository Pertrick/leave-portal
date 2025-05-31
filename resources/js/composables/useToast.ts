export function useToast() {
    return {
        success: (message: string) => {
            // You can implement your preferred toast notification here
            console.log('Success:', message)
        },
        error: (message: string) => {
            console.error('Error:', message)
        },
        warning: (message: string) => {
            console.warn('Warning:', message)
        },
        info: (message: string) => {
            console.info('Info:', message)
        },
    }
} 