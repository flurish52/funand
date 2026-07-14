<script setup>
import { Link } from '@inertiajs/vue3'
import MiniDemo from './miniDemo.vue'
import Squiggle from './squiggle.vue'
</script>

<template>
  <section class="relative bg-dot-grid">
    <div class="relative max-w-6xl mx-auto px-6 pt-16 pb-24 grid md:grid-cols-2 gap-16 items-center">
      <div>
        <p class="inline-block font-hand text-xl text-tertiary bg-card border border-border rounded-sm px-4 py-1 mb-6 -rotate-2 shadow-paper">
          for dates, loans &amp; favours you're too shy to ask in person
        </p>

        <h1 class="font-display font-black text-4xl sm:text-5xl leading-[1.08] text-foreground mb-6">
          <span class="inline-block animate-pop-in">Ask your one</span><br />
          <span class="inline-block animate-pop-in" style="animation-delay: .1s">real question.</span><br />
          <span class="relative inline-block text-primary animate-pop-in" style="animation-delay: .2s">
            Watch "no" shrink.
            <Squiggle color="primary" class-name="absolute -bottom-2 left-0 w-full h-3" />
          </span>
        </h1>

        <p class="text-lg text-foreground/70 mb-8 max-w-md">
          Build a one-question page for anything you're avoiding — a date, your
          money back, a favour owed. One honest button, one that runs for its
          life. Send the link. Let the button do the asking.
        </p>

        <div class="flex flex-wrap items-center gap-5">
          <Link
            href="/register"
            class="rounded-full bg-primary text-primary-foreground font-semibold px-7 py-3.5 shadow-paper hover:-translate-y-1 hover:-rotate-1 transition-all duration-200 active:translate-y-0"
          >
            Build your first ask — free
          </Link>
          <a href="#how-it-works" class="font-hand text-xl text-foreground/70 hover:text-foreground transition">
            see how it works ↓
          </a>
        </div>
      </div>

      <div class="relative flex justify-center md:justify-end">
        <MiniDemo />
      </div>
    </div>
  </section>
</template>
