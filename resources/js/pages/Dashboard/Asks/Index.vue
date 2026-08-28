<script setup>
/**
 * Dashboard list of the current user's asks, with a publish toggle,
 * quick copy-link action, inline response viewing, and search.
 */
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import ToggleSwitch from '@/components/Ask/Toggleswitch.vue'
import { Share2 } from '@lucide/vue'
import { route } from 'ziggy-js'

const props = defineProps({
    asks: { type: Array, required: true },
})

const selected = ref(null)
const toast = ref(null)
const query = ref('')
let toastTimer = null

const filteredAsks = computed(() => {
    const q = query.value.trim().toLowerCase()
    if (!q) return props.asks
    return props.asks.filter(
        (ask) =>
            ask.for_name?.toLowerCase().includes(q) ||
            ask.question?.toLowerCase().includes(q)
    )
})

function togglePublish(ask) {
    router.patch(route('asks.toggle-publish', ask.id), {}, { preserveScroll: true })
}

function destroy(ask) {
    if (confirm(`Delete the ask "${ask.for_name}"? This can't be undone.`)) {
        router.delete(route('asks.destroy', ask.id))
    }
}

// Single share action: tries the native share sheet first (best on
// mobile, and where multiple apps can be picked), only falls back to
// a plain clipboard copy if the Web Share API isn't available or the
// browser rejects it — this replaces a separate "copy link" button.
async function share(ask) {
    const url = `${window.location.origin}/for-${ask.slug}`
    const shareData = {
        title: ask.question,
        text: ask.share_text || ask.question,
        url,
    }

    if (navigator.share) {
        try {
            await navigator.share(shareData)
            return
        } catch (err) {
            if (err?.name === 'AbortError') return
        }
    }

    try {
        await navigator.clipboard.writeText(url)
        showToast('Link copied.')
    } catch (err) {
        console.error(err)
        showToast("Couldn't copy the link — try again.")
    }
}

function showToast(message) {
    toast.value = message
    clearTimeout(toastTimer)
    toastTimer = setTimeout(() => (toast.value = null), 2500)
}

function viewResponse(ask) {
    if (ask.response_status === 'pending') {
        showToast(`No response from ${ask.for_name} yet.`)
        return
    }

    router.get(route('asks.responses.index', ask.id))
}

function closeModal() {
    selected.value = null
}

function onKeydown(e) {
    if (e.key === 'Escape') closeModal()
}

const responseButtonClass = (status) => ({
    'border-tertiary/40 text-tertiary bg-tertiary/10': status === 'answered',
    'border-primary/40 text-primary bg-primary/10': status === 'opened',
    'border-border text-foreground/40': status === 'pending',
}[status])

const responseButtonLabel = {
    answered: 'View response',
    opened: 'Deciding…',
    pending: 'No response yet',
}

const avatarPalette = ['bg-primary', 'bg-tertiary', 'bg-secondary']
function avatarClass(id) {
    return avatarPalette[id % avatarPalette.length]
}
function avatarTextClass(id) {
    return avatarPalette[id % avatarPalette.length] === 'bg-secondary' ? 'text-secondary-foreground' : 'text-primary-foreground'
}

const timeToYes = computed(() => {
    const s = selected.value?.response?.seconds_to_yes
    if (!s && s !== 0) return null
    if (s < 60) return `${s}s`
    return `${Math.floor(s / 60)}m ${s % 60}s`
})
</script>

<template>
    <div class="max-w-3xl mx-auto p-6 space-y-6 relative" @keydown="onKeydown">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h1 class="font-display font-bold text-2xl text-foreground">Your asks</h1>
            <Link
                href="/asks/create"
                class="shrink-0 rounded-full bg-primary text-primary-foreground font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200 text-center"
            >
                + New ask
            </Link>
        </div>

        <div v-if="asks.length > 0" class="relative">
            <svg
                class="absolute left-3.5 top-1/2 -translate-y-1/2 text-foreground/35"
                width="16" height="16" viewBox="0 0 16 16" fill="none"
            >
                <circle cx="7" cy="7" r="5.2" stroke="currentColor" stroke-width="1.6" />
                <path d="M11 11L14.5 14.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
            <input
                v-model="query"
                type="text"
                placeholder="Search by name or question…"
                class="w-full rounded-full border-1.5 border-border bg-card pl-10 pr-9 py-2.5 text-sm outline-none transition-shadow focus:border-tertiary focus:ring-2 focus:ring-tertiary/15"
            />
            <button
                v-if="query"
                type="button"
                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-foreground/35 hover:text-foreground/60"
                aria-label="clear search"
                @click="query = ''"
            >
                ✕
            </button>
        </div>

        <div v-if="asks.length === 0" class="text-center py-16 rounded-lg border border-dashed border-border bg-card/60">
            <p class="font-hand text-2xl text-tertiary -rotate-1">Nothing here yet</p>
            <p class="text-sm text-foreground/50 mt-1 mb-4">Go on, ask somebody something</p>
            <Link
                href="/asks/create"
                class="rounded-full bg-primary py-4  text-primary-foreground font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
            >
                Let's do it 😋
            </Link>
        </div>

        <div v-else-if="filteredAsks.length === 0" class="text-center py-14 rounded-lg border border-dashed border-border bg-card/60">
            <p class="font-hand text-xl text-tertiary py-4 -rotate-1">nothing matches "{{ query }}"</p>
            <Link
                href="/asks/create"
                class="rounded-full bg-primary text-primary-foreground font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
            >
                Create for/with "{{ query }}"?😋
            </Link>
        </div>

        <div
            v-for="ask in filteredAsks"
            :key="ask.id"
            class="rounded-lg border border-border bg-card p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-paper"
        >
            <div class="flex items-start gap-3.5 min-w-0">
                <div class="shrink-0 w-10 h-10 rounded-full overflow-hidden flex items-center justify-center">
                    <img
                        v-if="ask.cover_image"
                        :src="`/storage/${ask.cover_image}`"
                        :alt="ask.for_name"
                        class="w-full h-full object-cover"
                    />
                    <div
                        v-else
                        class="w-full h-full flex items-center justify-center font-display font-semibold text-sm"
                        :class="[avatarClass(ask.id), avatarTextClass(ask.id)]"
                    >
                        {{ ask.for_name?.[0]?.toUpperCase() }}
                    </div>
                </div>
                <div class="min-w-0">
                    <p class="font-display font-semibold text-foreground truncate">For {{ ask.for_name }}</p>
                    <p class="text-sm text-foreground/60 truncate">{{ ask.question }}</p>
                    <p class="text-xs text-foreground/40 mt-1">
                        {{ ask.responses_count }} response(s) · created {{ ask.created_at }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 flex-wrap shrink-0 sm:pl-2">
                <button
                    type="button"
                    class="text-sm rounded-full border px-3 py-1 font-medium transition-colors"
                    :class="responseButtonClass(ask.response_status)"
                    @click="viewResponse(ask)"
                >
                    {{ responseButtonLabel[ask.response_status] }}
                </button>

                <div class="flex items-center gap-2 rounded-full border border-border px-3 py-1">
                    <ToggleSwitch :model-value="ask.is_published" @update:model-value="togglePublish(ask)" />
                    <span
                        class="text-sm font-medium"
                        :class="ask.is_published ? 'text-tertiary' : 'text-foreground/50'"
                    >
                        {{ ask.is_published ? 'Published' : 'Draft' }}
                    </span>
                </div>

                <button
                    v-if="ask.slug"
                    type="button"
                    class="flex items-center gap-1.5 text-sm rounded-full border border-border px-3 py-1 text-foreground/70 hover:border-tertiary hover:text-tertiary transition-colors"
                    @click="share(ask)"
                >
                    <Share2 class="size-3.5" />
                    Share
                </button>

                <Link
                    :href="`asks/${ask.id}/edit`"
                    class="text-sm rounded-full border border-border px-3 py-1 text-foreground/70 hover:border-primary hover:text-primary transition-colors"
                >
                    Edit
                </Link>

                <button
                    type="button"
                    class="text-sm rounded-full border border-destructive/40 text-destructive px-3 py-1 hover:bg-destructive/10 transition-colors"
                    @click="destroy(ask)"
                >
                    Delete
                </button>
            </div>
        </div>

        <!-- Toast for "no response yet" / copy confirmation -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="toast"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-foreground text-background text-sm font-medium rounded-full px-4 py-2 shadow-paper z-50"
            >
                {{ toast }}
            </div>
        </Transition>

        <!-- Response modal -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="opacity-0"
        >
            <div
                v-if="selected"
                class="fixed inset-0 bg-foreground/40 backdrop-blur-[2px] flex items-center justify-center px-4 z-50"
                @click.self="closeModal"
            >
                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-2"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div class="edge-torn bg-card rounded-lg border border-border w-full max-w-md p-7 pt-8 relative shadow-paper">
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute -top-2.5 left-1/2 -translate-x-1/2 h-5 w-16 rotate-2 bg-secondary/40 shadow-sm"
                            style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
                        ></span>

                        <button
                            @click="closeModal"
                            class="absolute top-5 right-5 text-foreground/40 hover:text-foreground/70 text-sm"
                            aria-label="close"
                        >
                            ✕
                        </button>

                        <p class="text-xs text-foreground/40 mb-1">{{ selected.for_name }}</p>
                        <p class="font-display font-semibold text-lg text-foreground mb-6 pr-6">{{ selected.question }}</p>

                        <div v-if="selected.response_status === 'opened'" class="text-center py-6">
                            <p class="text-sm text-foreground/60 mb-3">they've opened it and are still deciding.</p>
                            <p class="font-display text-4xl font-bold text-primary mb-1 animate-pulse">{{ selected.response.decline_taps }}</p>
                            <p class="text-xs text-foreground/40">times they've tapped decline so far</p>
                        </div>

                        <div v-else class="text-center py-2">
                            <div class="w-16 h-16 rounded-full bg-tertiary/15 flex items-center justify-center mx-auto mb-3">
                                <span class="text-2xl text-tertiary">&#10003;</span>
                            </div>
                            <p class="font-display font-semibold text-xl text-foreground mb-1">
                                {{ selected.for_name }} said yes
                            </p>
                            <p class="text-xs text-foreground/40 mb-5">
                                {{ new Date(selected.response.responded_at).toLocaleString() }}
                            </p>

                            <div class="grid grid-cols-2 gap-3 text-left mb-4">
                                <div class="bg-accent/30 border border-border/70 rounded-lg p-3">
                                    <p class="text-xs text-foreground/40 mb-1">decline taps</p>
                                    <p class="font-display text-xl font-semibold text-foreground">{{ selected.response.decline_taps }}</p>
                                </div>
                                <div class="bg-accent/30 border border-border/70 rounded-lg p-3">
                                    <p class="text-xs text-foreground/40 mb-1">time to yes</p>
                                    <p class="font-display text-xl font-semibold text-foreground">{{ timeToYes ?? '—' }}</p>
                                </div>
                            </div>

                            <div
                                v-if="selected.response.responded_date"
                                class="text-left bg-accent/30 border border-border/70 rounded-lg p-3"
                            >
                                <p class="text-xs text-foreground/40 mb-1">picked</p>
                                <p class="text-sm font-medium text-foreground">
                                    {{ selected.response.responded_date }} at {{ selected.response.responded_time }}
                                </p>
                                <p v-if="selected.response.venue" class="text-sm text-foreground/60 mt-1">
                                    {{ selected.response.venue.name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </div>
</template>
