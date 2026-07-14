<script setup>
/**
 * A strip of washi tape "holding down" a card or photo. Purely
 * decorative (aria-hidden). Notched ends + diagonal stripe texture so
 * it reads as a physical object, not a gradient chip.
 */
defineProps({
  color: { type: String, default: 'secondary' }, // 'primary' | 'secondary' | 'tertiary' | 'muted'
  width: { type: String, default: '5.5rem' },
  rotate: { type: String, default: '-4deg' },
  className: { type: String, default: '' },
})

const bg = {
  primary: 'bg-primary/25',
  secondary: 'bg-secondary/35',
  tertiary: 'bg-tertiary/25',
  muted: 'bg-foreground/10',
}
</script>

<template>
  <span
    aria-hidden="true"
    class="pointer-events-none absolute h-6 opacity-90 shadow-sm"
    :class="[bg[color], className]"
    :style="{
      width,
      transform: `rotate(${rotate})`,
      clipPath: 'polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)',
      backgroundImage:
        'repeating-linear-gradient(45deg, rgba(43,35,32,0.05) 0, rgba(43,35,32,0.05) 2px, transparent 2px, transparent 8px)',
    }"
  ></span>
</template>
