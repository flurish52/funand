<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    form: { type: Object, required: true },
    ask: { type: Object, default: null },
})

const origin = typeof window !== 'undefined' ? window.location.origin : ''
const fullLink = computed(() => (props.ask?.slug ? `${origin}/${props.ask.slug}` : ''))
const justCopied = ref(false)

function copyLink() {
    if (!fullLink.value) return
    navigator.clipboard.writeText(fullLink.value)
    justCopied.value = true
    setTimeout(() => (justCopied.value = false), 1600)
}
</script>

<template>
    <section class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-foreground mb-1.5">Final heading</label>
            <input v-model="form.final_heading" type="text" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
            <p v-if="form.errors.final_heading" class="text-destructive text-sm mt-1">{{ form.errors.final_heading }}</p>
        </div>
        <div>
            <label class="block text-sm font-semibold text-foreground mb-1.5">Final message</label>
            <textarea v-model="form.final_message" rows="3" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"></textarea>
            <p v-if="form.errors.final_message" class="text-destructive text-sm mt-1">{{ form.errors.final_message }}</p>
        </div>

        <div class="pt-2 border-t border-border/60 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-foreground mb-1.5">Your link</label>
                <div
                    v-if="fullLink"
                    class="flex items-center justify-between gap-3 rounded-lg border-1.5 border-border bg-background/40 h-11 pl-3.5 pr-1.5"
                >
                    <span class="truncate text-sm text-foreground/80">{{ fullLink }}</span>
                    <button
                        type="button"
                        class="shrink-0 text-xs font-semibold rounded-full border border-border px-3 py-1.5 text-foreground/60 hover:border-tertiary hover:text-tertiary transition-colors"
                        @click="copyLink"
                    >
                        {{ justCopied ? 'Copied!' : 'Copy' }}
                    </button>
                </div>
                <p v-else class="font-hand text-lg text-tertiary">
                    your link gets created automatically once you save
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-foreground mb-1.5">Share caption</label>
                <input v-model="form.share_text" type="text" placeholder="Something to go along with the link" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
            </div>

            <label class="flex items-center gap-2.5">
                <input type="checkbox" v-model="form.is_published" class="accent-primary w-4 h-4" />
                <span class="text-sm font-semibold text-foreground">Publish ask (make this live), everyone with the link can respond!</span>
            </label>
        </div>
    </section>
</template>

<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}
.shake {
    animation: shake 0.35s;
}
</style>
