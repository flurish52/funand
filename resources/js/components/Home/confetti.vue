<script setup>
/**
 * Tiny confetti burst. Parent triggers it imperatively:
 *   <Confetti ref="confetti" />
 *   confetti.value.burst()
 */
import { ref } from 'vue'

const particles = ref([])
let idCounter = 0

function burst() {
  const emojis = ['🎉', '✨', '🧡', '💛']
  const batch = Array.from({ length: 14 }).map(() => ({
    id: idCounter++,
    emoji: emojis[Math.floor(Math.random() * emojis.length)],
    x: (Math.random() - 0.5) * 220,
    y: -(Math.random() * 160 + 60),
    rotate: (Math.random() - 0.5) * 200,
  }))

  particles.value.push(...batch)
  setTimeout(() => {
    const ids = new Set(batch.map((p) => p.id))
    particles.value = particles.value.filter((p) => !ids.has(p.id))
  }, 900)
}

defineExpose({ burst })
</script>

<template>
  <div class="relative w-0 h-0 pointer-events-none">
    <span
      v-for="p in particles"
      :key="p.id"
      class="absolute left-1/2 top-1/2 text-xl select-none"
      :style="{
        '--tx': p.x + 'px',
        '--ty': p.y + 'px',
        '--rot': p.rotate + 'deg',
        animation: 'confetti-burst 0.9s ease-out forwards',
      }"
    >{{ p.emoji }}</span>
  </div>
</template>

<style scoped>
@keyframes confetti-burst {
  0% {
    transform: translate(-50%, -50%) rotate(0deg);
    opacity: 1;
  }
  100% {
    transform: translate(calc(-50% + var(--tx)), calc(-50% + var(--ty))) rotate(var(--rot));
    opacity: 0;
  }
}
</style>
