<script setup>
/**
 * A small handwritten note tag — like something scrawled on a sticky
 * note and stuck to the page. Used to call out one playful fact.
 */
defineProps({
  text: { type: String, required: true },
  className: { type: String, default: '' },
})
</script>

<template>
  <span
    class="inline-flex items-center gap-1 rounded-sm bg-secondary px-3 py-1 font-hand text-lg font-bold leading-none text-secondary-foreground shadow-paper animate-sway whitespace-nowrap"
    :class="className"
  >
    {{ text }}
  </span>
</template>
