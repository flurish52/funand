<script setup>
import { Link } from '@inertiajs/vue3'
import Squiggle from './squiggle.vue'
</script>

<template>
  <header class="sticky top-0 z-30 border-b border-border bg-background/90 backdrop-blur-sm">
    <nav class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
      <Link href="/" class="group relative font-display font-bold text-2xl text-foreground">
          Coa<span class="text-primary">xed</span>
        <Squiggle
          color="primary"
          class-name="absolute -bottom-1.5 left-0 w-full h-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
        />
      </Link>

      <div class="flex items-center gap-3">
        <Link
          href="/login"
          class="hidden sm:inline-block text-sm font-semibold text-foreground/70 hover:text-foreground transition"
        >
          Log in
        </Link>
        <Link
          href="/register"
          class="rounded-full bg-primary text-primary-foreground text-sm font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
        >
          Create yours
        </Link>
      </div>
    </nav>
  </header>
</template>
