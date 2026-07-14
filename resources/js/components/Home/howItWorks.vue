<script setup>
import RevealOnScroll from './revealOnScroll.vue'
import Pin from './pin.vue'

const steps = [
  {
    n: '01',
    title: 'Write your ask',
    body: 'Type the question, add a photo if you want one, write both button labels. Yes, even the one nobody gets to press.',
  },
  {
    n: '02',
    title: 'Add the details',
    body: 'Optional: a date range and a short list of venues, so the answer you get is one you can actually plan around.',
  },
  {
    n: '03',
    title: 'Share your link',
    body: 'Every ask gets its own address. Post it, DM it, or tape it to their laptop.',
  },
  {
    n: '04',
    title: 'Watch "no" shrink',
    body: 'The button that says no gets smaller with every tap, then disappears. The other one takes the whole screen.',
  },
]
</script>

<template>
  <section id="how-it-works" class="max-w-6xl mx-auto px-6 py-20">
    <RevealOnScroll>
      <h2 class="font-display font-bold text-3xl text-foreground mb-2">How it works</h2>
      <p class="font-hand text-xl text-tertiary mb-12 max-w-lg">four steps, nothing to install</p>
    </RevealOnScroll>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <RevealOnScroll v-for="(step, i) in steps" :key="step.n" :delay="i * 100">
        <div
          class="relative h-full rounded-lg border border-border bg-card p-6 pt-8 shadow-paper transition-transform duration-300 hover:-translate-y-1.5"
          :class="i % 2 === 0 ? '-rotate-1' : 'rotate-1'"
        >
          <Pin :color="i % 2 === 0 ? 'primary' : 'tertiary'" class-name="-top-2.5 left-1/2 -translate-x-1/2" />
          <span class="font-display font-bold text-2xl text-secondary">{{ step.n }}</span>
          <h3 class="font-display font-semibold text-lg text-foreground mt-3 mb-2">{{ step.title }}</h3>
          <p class="text-sm text-foreground/60 leading-relaxed">{{ step.body }}</p>
        </div>
      </RevealOnScroll>
    </div>
  </section>
</template>
