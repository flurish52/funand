<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import { useDuration } from '@/composables/useDuration'

const props = defineProps({
    ask: { type: Object, required: true },
    responses: { type: Array, default: () => [] },
})

const { formatDuration } = useDuration()

const hasResponses = computed(() => props.responses.length > 0)

function formatDateTime(dateString) {
    if (!dateString) return null
    return new Date(dateString).toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>
    <Head :title="`Responses — ${ask.for_name || ask.question}`" />
    <div class="max-w-2xl mx-auto p-6">
        <div class="flex items-center gap-3 mb-2">
            <svg aria-hidden="true" class="shrink-0 animate-bob" width="24" height="24" viewBox="0 0 22 22">
                <circle cx="11" cy="9" r="7" class="fill-primary" />
                <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
                <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <h1 class="font-display font-bold text-2xl text-foreground">Responses</h1>
        </div>
        <p class="text-sm text-foreground/55 mb-8 ml-9">
            {{ ask.question }} — {{ ask.for_name }}
        </p>

        <!-- Empty state -->
        <div
            v-if="!hasResponses"
            class="rounded-xl border border-dashed border-border bg-card/40 p-10 text-center"
        >
            <p class="font-hand text-xl text-tertiary -rotate-1">no one's answered yet</p>
            <p class="mt-2 text-sm text-foreground/50">Once someone opens your link and responds, it'll show up here.</p>
        </div>

        <!-- Response list -->
        <div v-else class="space-y-4">
            <div
                v-for="response in responses"
                :key="response.id"
                class="rounded-xl border border-border bg-card shadow-paper p-5"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-foreground">
                            {{ formatDateTime(response.responded_at) || 'Not yet responded' }}
                        </p>
                        <p v-if="response.venue" class="mt-1 text-sm text-foreground/70">
                            📍 {{ response.venue.name }}
                            <span v-if="response.venue.address" class="text-foreground/45">— {{ response.venue.address }}</span>
                        </p>
                    </div>

                    <div
                        class="shrink-0 rounded-full border border-red-500/30 bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-500 whitespace-nowrap"
                    >
                      Decline taps  {{ response.decline_taps}}
                    </div>
                </div>

                <p
                    v-if="response.decline_taps > 0"
                    class="mt-3 font-hand text-base text-primary"
                >
                    tried to say no {{ response.decline_taps }} time{{ response.decline_taps > 1 ? 's' : '' }} first 😄
                </p>
            </div>
        </div>

        <div class="mt-8">
            <Link :href="`/asks`" class="text-sm font-semibold text-primary hover:opacity-70">
                ← Back to ask
            </Link>
        </div>
    </div>
</template>
