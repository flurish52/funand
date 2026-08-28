<script setup>

import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { Share2 } from '@lucide/vue'

const props = defineProps({
    ask: { type: Object, required: true },
    slug: { type: String, required: true },
})

const emit = defineEmits(['accepted'])

const arena = ref(null)
const noBtn = ref(null)

const offset = reactive({ x: 0, y: 0 })
const declineTaps = ref(0)
const noScale = computed(() => Math.max(0, 1 - declineTaps.value * 0.16))
const noGone = computed(() => noScale.value <= 0.08)
const dodgeCount = ref(0)

const yesScale = computed(() => 1 + declineTaps.value * 0.16)

const yesLifted = ref(false)
const yesPressed = ref(false)

const yesTransform = computed(() => {
    const lift = yesLifted.value ? -3 : 0
    const press = yesPressed.value ? 0.95 : 1
    return `translate(-50%, calc(-50% + ${lift}px)) scale(${yesScale.value * press})`
})

let rafPending = false
let lastEvent = null

function handlePointerMove(e) {
    lastEvent = e
    if (rafPending) return
    rafPending = true
    requestAnimationFrame(() => {
        rafPending = false
        if (lastEvent) repel(lastEvent)
    })
}

function repel(e) {
    if (!arena.value || !noBtn.value || noGone.value) return

    const arenaRect = arena.value.getBoundingClientRect()
    const btnRect = noBtn.value.getBoundingClientRect()
    const btnCx = btnRect.left + btnRect.width / 2
    const btnCy = btnRect.top + btnRect.height / 2

    const dx = btnCx - e.clientX
    const dy = btnCy - e.clientY
    const dist = Math.hypot(dx, dy) || 1

    const SAFE_RADIUS = 130
    if (dist >= SAFE_RADIUS) return

    const angle = Math.atan2(dy, dx)
    const push = (SAFE_RADIUS - dist) * 1.15

    const maxX = (arenaRect.width - btnRect.width) / 2 - 4
    const maxY = (arenaRect.height - btnRect.height) / 2 - 4

    offset.x = clamp(offset.x + Math.cos(angle) * push, -maxX, maxX)
    offset.y = clamp(offset.y + Math.sin(angle) * push, -maxY, maxY)
    dodgeCount.value += 1
}

function clamp(v, min, max) {
    return Math.min(max, Math.max(min, v))
}

function tapDecline() {
    if (noGone.value) return
    declineTaps.value += 1
    const jump = 26
    const a = Math.random() * Math.PI * 2
    offset.x = clamp(offset.x + Math.cos(a) * jump, -200, 200)
    offset.y = clamp(offset.y + Math.sin(a) * jump, -120, 120)
}

function tapAccept() {
    emit('accepted', { declineTaps: declineTaps.value })
}

onMounted(() => {
    window.addEventListener('mousemove', handlePointerMove, { passive: true })
})
onUnmounted(() => {
    window.removeEventListener('mousemove', handlePointerMove)
})

// --- Share -------------------------------------------------------
// Single button: tries the native share sheet first (best experience
// on mobile), and only falls back to a plain clipboard copy if the
// Web Share API isn't available or the user's browser rejects it —
// no separate "copy link" button needed alongside it.
const justCopied = ref(false)

const shareUrl = computed(() => `${window.location.origin}/for-${props.slug}`)

async function share() {
    const shareData = {
        title: props.ask.question,
        text: props.ask.share_text || props.ask.question,
        url: shareUrl.value,
    }

    if (navigator.share) {
        try {
            await navigator.share(shareData)
            return
        } catch (err) {
            // user cancelled the share sheet — do nothing, don't fall back
            if (err?.name === 'AbortError') return
        }
    }

    // Fallback: copy to clipboard
    try {
        await navigator.clipboard.writeText(shareUrl.value)
        justCopied.value = true
        setTimeout(() => (justCopied.value = false), 1600)
    } catch (err) {
        console.error(err)
    }
}
</script>

<template>
    <div class="flex w-full max-w-md flex-1 min-h-0 flex-col items-center justify-center">
        <!-- profile card -->
        <div class="relative mb-4 flex shrink-0 flex-col items-center">
            <div class="relative rotate-[-3deg]">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute -top-3 left-1/2 h-6 w-20 -translate-x-1/2 rotate-2 bg-secondary/40 shadow-sm"
                    style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
                ></span>
                <div class="rounded-sm border-4 border-card bg-card p-2 shadow-paper">
                    <div class="h-[min(11rem,20vh)] w-[min(11rem,20vh)] overflow-hidden rounded-sm bg-muted">
                        <img
                            v-if="ask.cover_image"
                            :src="ask.cover_image"
                            :alt="ask.for_name"
                            class="h-full w-full object-cover"
                        />
                        <svg v-else viewBox="0 0 200 200" class="h-full w-full">
                            <rect width="200" height="200" fill="#EFE2C8" />
                            <circle cx="100" cy="78" r="34" fill="#D6472C" fill-opacity="0.25" />
                            <path d="M40 175c8-42 44-58 60-58s52 16 60 58" fill="#1F6F5C" fill-opacity="0.2" />
                        </svg>
                    </div>
                    <p class="pt-2 pb-0.5 text-center font-hand text-xl text-foreground/80">
                        for {{ ask.for_name }}
                    </p>
                </div>
            </div>
        </div>

        <h1 class="mb-5 shrink-0 text-center font-display text-2xl font-bold leading-snug text-foreground sm:text-3xl">
            {{ ask.question }}
        </h1>

        <div ref="arena" class="relative mx-auto h-36 w-full max-w-sm shrink-0 select-none sm:h-40">
            <button
                type="button"
                class="absolute left-[38%] top-1/2 rounded-full bg-primary px-8 py-3.5 text-base font-semibold text-primary-foreground shadow-paper sm:px-10 sm:py-4 sm:text-lg"
                :style="{
                    transform: yesTransform,
                    transformOrigin: 'center',
                    transition: 'transform 150ms ease-out',
                }"
                @mouseenter="yesLifted = true"
                @mouseleave="yesLifted = false; yesPressed = false"
                @mousedown="yesPressed = true"
                @mouseup="yesPressed = false"
                @click="tapAccept"
            >
                {{ ask.accept_label }}
            </button>

            <button
                v-if="!noGone"
                ref="noBtn"
                type="button"
                class="absolute rounded-full border-2 border-foreground/20 bg-card px-6 py-2.5 font-semibold text-foreground/60 shadow-paper sm:px-7 sm:py-3"
                :style="{
                    left: '62%',
                    top: '50%',
                    transform: `translate(calc(-50% + ${offset.x}px), calc(-50% + ${offset.y}px)) scale(${noScale})`,
                    transition: 'transform 90ms ease-out',
                }"
                @click="tapDecline"
            >
                {{ ask.decline_label }}
            </button>
        </div>

        <p class="mt-2 h-5 shrink-0 text-center font-hand text-base text-foreground/40">
            <template v-if="dodgeCount > 4 && !noGone">it really doesn't want to be clicked</template>
            <template v-else-if="declineTaps > 0 && !noGone">{{ declineTaps }} tap{{ declineTaps > 1 ? 's' : '' }}, still no</template>
            <template v-else-if="noGone">that one's given up entirely</template>
        </p>

        <button
            type="button"
            class="mt-4 flex shrink-0 items-center gap-2 rounded-full border-1.5 border-border bg-card px-5 py-2 text-sm font-semibold text-foreground/70 shadow-sm transition-colors hover:border-tertiary hover:text-tertiary"
            @click="share"
        >
            <Share2 class="size-4" />
            {{ justCopied ? 'Link copied!' : 'Share' }}
        </button>
    </div>
</template>
