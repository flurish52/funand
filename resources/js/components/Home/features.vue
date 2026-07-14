<script setup>
import RevealOnScroll from './revealOnScroll.vue'
import Squiggle from './squiggle.vue'

const features = [
  { emoji: '✏️', title: 'Fully your words', body: 'The question, the photo, both button labels, the closing line — you write every word of it.' },
  { emoji: '📅', title: 'Real dates only', body: 'Set a first and last possible date, so "whenever" turns into an actual plan.' },
  { emoji: '📍', title: 'Optional venue picker', body: 'Give them a short list of places instead of an open text box.' },
  { emoji: '🔀', title: 'One tap to publish', body: "Flip it on when you're ready to send the link, off whenever you're not." },
  { emoji: '🔗', title: 'Your own link', body: 'Every ask lives at its own address: /for-whatever-you-typed.' },
  { emoji: '🙈', title: 'A decline counter', body: 'We quietly count every tap on the button that never works. Bragging rights, built in.' },
]
</script>

<template>
  <section class="max-w-6xl mx-auto px-6 py-20">
    <RevealOnScroll>
      <h2 class="relative inline-block font-display font-bold text-3xl text-foreground mb-2">
        Everything you need
        <Squiggle color="secondary" class-name="absolute -bottom-1 left-0 w-full h-2" />
      </h2>
      <p class="font-hand text-xl text-tertiary mb-12 max-w-lg mt-3">nothing you don't — built to fill in once and share in seconds</p>
    </RevealOnScroll>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-10">
      <RevealOnScroll v-for="(f, i) in features" :key="f.title" :delay="i * 80">
        <div class="group flex gap-4">
          <span class="text-2xl transition-transform duration-300 group-hover:scale-125 group-hover:-rotate-6">{{ f.emoji }}</span>
          <div>
            <h3 class="font-display font-semibold text-foreground mb-1">{{ f.title }}</h3>
            <p class="text-sm text-foreground/60 leading-relaxed">{{ f.body }}</p>
          </div>
        </div>
      </RevealOnScroll>
    </div>
  </section>
</template>
