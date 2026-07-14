<script setup>
import { onMounted, ref } from 'vue'

const props = defineProps({
    ask: { type: Object, required: true },
})

const particles = ref([])

onMounted(() => {
    const emojis = ['🎉', '✨', '🧡', '💛', '🎊']
    particles.value = Array.from({ length: 24 }).map((_, i) => ({
        id: i,
        emoji: emojis[Math.floor(Math.random() * emojis.length)],
        x: (Math.random() - 0.5) * 340,
        delay: Math.random() * 0.3,
        rotate: (Math.random() - 0.5) * 360,
    }))
})
</script>

<template>
    <div class="relative flex w-full max-w-md flex-col items-center text-center">
    <span
        v-for="p in particles"
        :key="p.id"
        class="pointer-events-none absolute top-0 left-1/2 text-2xl"
        :style="{
        '--tx': p.x + 'px',
        '--rot': p.rotate + 'deg',
        animationDelay: p.delay + 's',
        animation: 'confetti-fall 1.8s ease-out forwards',
      }"
    >{{ p.emoji }}</span
    >

        <h2 class="mb-3 font-display text-4xl font-black text-foreground">
            {{ ask.final_heading || 'Hurray!' }}
        </h2>
        <p class="max-w-sm text-lg text-foreground/70">
            {{ ask.final_message || 'I knew you would say yes!' }}
        </p>
    </div>
</template>

<style scoped>
@keyframes confetti-fall {
    0% {
        transform: translate(-50%, 0) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translate(calc(-50% + var(--tx)), 260px) rotate(var(--rot));
        opacity: 0;
    }
}
</style>
