<script setup>
import RevealOnScroll from './revealOnScroll.vue'
import Tape from './tape.vue'

const cases = [
  {
    emoji: '🌹',
    title: 'The date',
    question: '"Free Friday? I want to take you out."',
    body: 'Add two or three venues and let them pick one. Romance, with a dropdown.',
  },
  {
    emoji: '💸',
    title: 'The loan',
    question: '"When is my money reaching me?"',
    body: 'Swap Yes / No for Now / I don\'t know. Only one of those is a real answer.',
  },
  {
    emoji: '🙏',
    title: 'The favour',
    question: '"Can you cover my shift Saturday?"',
    body: 'Skip the date step entirely — go straight from ask to thank you.',
  },
  {
    emoji: '🎉',
    title: 'Whatever you want',
    question: '"..."',
    body: 'Every word on the page is yours. If you can ask it, Coaxed can ask it for you.',
  },
]
</script>

<template>
  <section class="bg-accent/40 border-y border-border">
    <div class="max-w-6xl mx-auto px-6 py-20">
      <RevealOnScroll>
        <h2 class="font-display font-bold text-3xl text-foreground mb-2">Ask for anything</h2>
        <p class="font-hand text-xl text-tertiary mb-12 max-w-lg">a few things people have asked — yours doesn't have to look like any of these</p>
      </RevealOnScroll>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <RevealOnScroll v-for="(c, i) in cases" :key="c.title" :delay="i * 100">
          <div
            class="relative h-full rounded-lg bg-card border border-border p-6 pt-8 flex flex-col shadow-paper transition-transform duration-300 hover:-translate-y-1.5 hover:rotate-0 hover:scale-[1.02]"
            :class="i % 2 === 0 ? '-rotate-1' : 'rotate-1'"
          >
            <Tape
              :color="i % 2 === 0 ? 'primary' : 'secondary'"
              width="4rem"
              :rotate="i % 2 === 0 ? '-8deg' : '6deg'"
              class-name="-top-2.5 left-1/2 -translate-x-1/2"
            />
            <span class="text-3xl mb-4">{{ c.emoji }}</span>
            <h3 class="font-display font-semibold text-lg text-foreground mb-1">{{ c.title }}</h3>
            <p class="text-sm text-primary font-medium mb-3">{{ c.question }}</p>
            <p class="text-sm text-foreground/60 leading-relaxed mt-auto">{{ c.body }}</p>
          </div>
        </RevealOnScroll>
      </div>
    </div>
  </section>
</template>
