<script setup>
/**
 * Public marketing landing page (/) — composed entirely of section
 * components under Components/Home for easy tracing.
 */
import { Head } from '@inertiajs/vue3'
import Grain from '@/components/Home/grain.vue'
import Navbar from '@/components/Home/navbar.vue'
import Hero from '@/components/Home/hero.vue'
import StatsTicker from '@/components/Home/statsTicker.vue'
import HowItWorks from '@/components/Home/howItWorks.vue'
import UseCases from '@/components/Home/useCases.vue'
import Features from '@/components/Home/features.vue'
import FinalCta from '@/components/Home/finalCta.vue'
import Footer from '@/components/Home/footer.vue'
</script>

<template>
  <Head title="Coaxed — Home." />

  <div class="relative min-h-screen bg-background font-sans overflow-x-hidden">
    <Grain />
    <div class="relative z-10">
      <Navbar />
      <Hero />
      <StatsTicker />
      <HowItWorks />
      <UseCases />
      <Features />
      <FinalCta />
      <Footer />
    </div>
  </div>
</template>
