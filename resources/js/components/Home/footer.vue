<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <footer class="border-t border-border">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div>
        <p class="font-display font-bold text-lg text-foreground">Coa<span class="text-primary">xed</span></p>
        <p class="font-hand text-lg text-foreground/50 -mt-0.5">one button that works, one that gives up</p>
      </div>

      <div class="flex items-center gap-6 text-sm text-foreground/60">
        <Link href="/register" class="hover:text-foreground transition">Create an ask</Link>
        <Link href="/login" class="hover:text-foreground transition">Log in</Link>
      </div>
    </div>
  </footer>
</template>
