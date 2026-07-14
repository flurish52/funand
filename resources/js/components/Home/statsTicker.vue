<script setup>
/**
 * Social-proof strip: three count-up stats, then an infinite marquee
 * ribbon. Numbers are placeholders — wire them to real data (or just
 * edit them) once you have it. Don't ship made-up stats as fact.
 */
import CountUp from './countUp.vue'

const phrases = [
  'Zero awkward silences',
  'One tap, no maybe',
  'Dates, loans, everything between',
  'Say it once, hear yes',
]
</script>

<template>
  <section class="border-y border-border">
    <div class="max-w-6xl mx-auto px-6 py-10 grid grid-cols-3 gap-6 text-center">
      <div v-for="(s, i) in [
        { to: 120, suffix: '+', label: 'asks sent' },
        { to: 98, suffix: '%', label: 'said yes eventually' },
        { to: 2, suffix: ' min', label: 'to build one' },
      ]" :key="s.label" :class="i === 1 ? '-rotate-1' : i === 2 ? 'rotate-1' : ''">
        <p class="font-display font-black text-3xl text-primary"><CountUp :to="s.to" :suffix="s.suffix" /></p>
        <p class="font-hand text-lg text-foreground/60 mt-0.5">{{ s.label }}</p>
      </div>
    </div>

    <div class="relative flex overflow-hidden bg-tertiary py-3">
      <div class="flex whitespace-nowrap animate-marquee">
        <span v-for="n in 2" :key="n" class="flex gap-10 pr-10">
          <span
            v-for="(phrase, i) in phrases"
            :key="i"
            class="font-display font-semibold text-tertiary-foreground text-sm uppercase tracking-wide"
          >
            {{ phrase }} ✦
          </span>
        </span>
      </div>
    </div>
  </section>
</template>
