<script setup>
defineProps({
  color: { type: String, default: 'primary' }, // 'primary' | 'secondary' | 'tertiary'
  className: { type: String, default: '' },
})

const stroke = {
  primary: 'stroke-primary',
  secondary: 'stroke-secondary',
  tertiary: 'stroke-tertiary',
}
</script>

<template>
  <svg
    aria-hidden="true"
    viewBox="0 0 220 16"
    class="pointer-events-none"
    :class="className"
    preserveAspectRatio="none"
  >
    <path
      d="M2 10.5C36 3 70 3 108 8.5C146 14 182 3 218 6"
      fill="none"
      :class="[stroke[color], 'animate-draw']"
      stroke-width="5"
      stroke-linecap="round"
      pathLength="240"
      stroke-dasharray="240"
    />
  </svg>
</template>
