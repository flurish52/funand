<script setup>
import { Link } from '@inertiajs/vue3'
import RevealOnScroll from './revealOnScroll.vue'
</script>

<template>
  <section class="max-w-6xl mx-auto px-6 pb-24">
    <RevealOnScroll>
      <div class="relative rounded-lg border-2 border-foreground/10 bg-card px-8 py-14 text-center shadow-paper overflow-hidden">
        <svg aria-hidden="true" class="absolute top-5 right-5 w-14 h-16 opacity-70 -rotate-6" viewBox="0 0 56 64">
          <rect x="1" y="1" width="54" height="62" rx="3" fill="none" stroke="#1F6F5C" stroke-width="2" stroke-dasharray="3 3" />
          <circle cx="28" cy="28" r="12" fill="none" stroke="#1F6F5C" stroke-width="2" />
          <path d="M20 40h16M22 46h12" stroke="#1F6F5C" stroke-width="2" stroke-linecap="round" />
        </svg>

        <p class="font-hand text-2xl text-tertiary mb-2 -rotate-1">P.S. —</p>
        <h2 class="font-display font-bold text-3xl sm:text-4xl text-foreground mb-3 max-w-lg mx-auto">
          Somebody's phone is waiting for this text.
        </h2>
        <p class="text-foreground/60 mb-8 max-w-md mx-auto">
          Takes about two minutes to build. Free to send. No card, no signup
          fee for them — the only cost is your nerve.
        </p>

        <Link
          href="/register"
          class="inline-block rounded-full bg-primary text-primary-foreground font-semibold px-8 py-3.5 shadow-paper hover:-translate-y-1 hover:rotate-1 transition-all duration-200"
        >
          Start your ask
        </Link>
      </div>
    </RevealOnScroll>
  </section>
</template>
