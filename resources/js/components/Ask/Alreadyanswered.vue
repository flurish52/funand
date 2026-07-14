<script setup>
import BrandFooterCta from "@/components/Ask/BrandFooterCta.vue";

defineProps({
    ask: { type: Object, required: true },
})
</script>

<template>
    <div class="w-full max-w-md rounded-lg edge-torn border border-border bg-card p-8 text-center shadow-paper">
        <h2 class="mb-3 font-display text-2xl font-bold text-foreground">
            This one's already been answered
        </h2>
        <p class="text-foreground/60">
            {{ ask.for_name }} already responded to this ask — it's a one-time thing.
        </p>
    <BrandFooterCta :creator-name="ask.creator_name" />
    </div>
</template>
