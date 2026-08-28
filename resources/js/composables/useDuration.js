export function useDuration() {
    /**
     * Formats a duration in seconds as a short human-readable string.
     * e.g. 45 -> "45s", 125 -> "2m 5s", 3720 -> "1h 2m"
     */
    function formatDuration(totalSeconds) {
        if (totalSeconds === null || totalSeconds === undefined) return null

        const seconds = Math.max(0, Math.round(totalSeconds))
        const hours = Math.floor(seconds / 3600)
        const minutes = Math.floor((seconds % 3600) / 60)
        const secs = seconds % 60

        if (hours > 0) return `${hours}h ${minutes}m`
        if (minutes > 0) return `${minutes}m ${secs}s`
        return `${secs}s`
    }

    return { formatDuration }
}
