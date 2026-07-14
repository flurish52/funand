<script setup>
/**
 * Counts up from 0 to `to` once it scrolls into view. Used for the
 * stats row — purely a display effect, wrap real numbers in it.
 */
import { ref, watch } from 'vue'
import { useReveal } from '@/Composables/useReveal'

const props = defineProps({
  to: { type: Number, required: true },
  suffix: { type: String, default: '' },
  duration: { type: Number, default: 1200 },
})

const { target, isVisible } = useReveal()
const display = ref(0)

watch(isVisible, (visible) => {
  if (!visible) return
  const start = performance.now()

  function tick(now) {
    const progress = Math.min((now - start) / props.duration, 1)
    display.value = Math.floor(progress * props.to)
    if (progress < 1) requestAnimationFrame(tick)
    else display.value = props.to
  }

  requestAnimationFrame(tick)
})
</script>

<template>
  <span ref="target">{{ display }}{{ suffix }}</span>
</template>
