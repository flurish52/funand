<script setup>
defineProps({
  color: { type: String, default: 'primary' }, // 'primary' | 'tertiary'
  className: { type: String, default: '' },
})
</script>

<template>
  <svg
    aria-hidden="true"
    width="22"
    height="22"
    viewBox="0 0 22 22"
    class="pointer-events-none absolute animate-bob drop-shadow-sm"
    :class="className"
  >
    <circle cx="11" cy="9" r="7" :class="color === 'primary' ? 'fill-primary' : 'fill-tertiary'" />
    <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
    <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5" stroke-linecap="round" />
  </svg>
</template>
