export function useDateFormat() {
    const formatDate = (dateString: string | null | undefined): string => {
        if (!dateString) return 'Not assigned'
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    }

    return {
        formatDate
    }
} 