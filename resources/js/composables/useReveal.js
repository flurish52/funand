import { ref, onMounted, onUnmounted } from 'vue'

/**
 * Tiny scroll-reveal composable. Watches `target` and flips `isVisible`
 * to true the first time it enters the viewport, then stops watching.
 * Used by RevealOnScroll.vue — kept separate so any component can use
 * the raw values if it needs custom animation instead of the default.
 */
export function useReveal(options = {}) {
  const target = ref(null)
  const isVisible = ref(false)
  let observer

  onMounted(() => {
    observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          isVisible.value = true
          observer.disconnect()
        }
      },
      { threshold: options.threshold ?? 0.15 }
    )
    if (target.value) observer.observe(target.value)
  })

  onUnmounted(() => observer?.disconnect())

  return { target, isVisible }
}
