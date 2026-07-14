<script setup>
/**
 * Whole-page paper texture: a very faint grain (SVG feTurbulence) laid
 * over the dot-grid background utility. This is the thing that stops
 * the page reading as a flat digital gradient — it's the one "signature"
 * texture, used once, fixed behind everything else.
 */
</script>

<template>
  <div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 opacity-[0.05] mix-blend-multiply">
    <svg width="100%" height="100%">
      <filter id="onlyyes-grain">
        <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch" />
        <feColorMatrix type="saturate" values="0" />
      </filter>
      <rect width="100%" height="100%" filter="url(#onlyyes-grain)" />
    </svg>
  </div>
</template>
