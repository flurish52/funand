<script setup>
defineProps({
    creatorName: { type: String, default: null },
})
</script>

<template>
    <div class="mt-14 text-center">
        <p class="font-hand text-lg text-foreground/40">
            <template v-if="creatorName"> <span class="font-bold text-primary">{{ creatorName }}</span>  made this for specially with love, Would you love to make yours?
                using  </template>
            <template v-else>OnlyYes</template>
        </p>
        <a
            href="/register"
            class="mt-2 inline-block rounded-full border border-foreground/15 px-5 py-2 text-sm font-semibold text-foreground/60 transition-colors hover:border-primary hover:text-primary"
        >
            Make yours specially for someone →
        </a>
    </div>
</template>
