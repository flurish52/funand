<script setup>
/**
 * Wraps any block of content and fades/settles it into place the first
 * time it scrolls into view. Use `delay` to stagger items in a grid.
 */
import { useReveal } from '@/Composables/useReveal'

defineProps({
  delay: { type: Number, default: 0 },
})

const { target, isVisible } = useReveal()
</script>

<template>
  <div
    ref="target"
    class="transition-all duration-700 ease-out"
    :class="isVisible ? 'opacity-100 translate-y-0 rotate-0' : 'opacity-0 translate-y-6 -rotate-1'"
    :style="{ transitionDelay: `${delay}ms` }"
  >
    <slot />
  </div>
</template>
