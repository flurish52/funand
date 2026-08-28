<script setup>

import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    ask: { type: Object, required: true },
})

const emit = defineEmits(['accepted'])

const arena = ref(null)
const noBtn = ref(null)

const offset = reactive({ x: 0, y: 0 })
const declineTaps = ref(0)
const noScale = computed(() => Math.max(0, 1 - declineTaps.value * 0.16))
const noGone = computed(() => noScale.value <= 0.08)
const dodgeCount = ref(0)

// Inverse of noScale, same rate: taps=0 -> 1, taps=1 -> 1.16, etc.
const yesScale = computed(() => 1 + declineTaps.value * 0.16)

// Hover/press lift is handled in JS now instead of Tailwind's
// hover:/active: classes, since inline style (needed for the dynamic
// scale) would otherwise clobber those utility transforms.
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

// If it ever does get clicked/tapped — mouse got lucky, or it's a
// touch device where there was nothing to dodge with in the first
// place — treat it as a real decline tap: shrink it a notch (and
// grow the accept button a matching notch).
function tapDecline() {
    if (noGone.value) return
    declineTaps.value += 1
    // a little startled jump in a random direction on top of the repel offset
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
</script>

<template>
    <div class="w-full max-w-md">
        <!-- profile card -->
        <div class="relative mb-10 flex flex-col items-center">
            <div class="relative rotate-[-3deg]">
        <span
            aria-hidden="true"
            class="pointer-events-none absolute -top-3 left-1/2 h-6 w-20 -translate-x-1/2 rotate-2 bg-secondary/40 shadow-sm"
            style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
        ></span>
                <div class="rounded-sm border-4 border-card bg-card p-2 shadow-paper">
                    <div class="h-56 w-56 overflow-hidden rounded-sm bg-muted sm:h-64 sm:w-64">
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
                    <p class="pt-3 pb-1 text-center font-hand text-2xl text-foreground/80">
                        for {{ ask.for_name }}
                    </p>
                </div>
            </div>
        </div>

        <h1 class="mb-10 text-center font-display text-3xl font-bold leading-snug text-foreground sm:text-4xl">
            {{ ask.question }}
        </h1>

        <!-- the arena: fixed-size dodge room. On phones there's no cursor to
             repel from, so this just becomes the tap target with room to jump. -->
        <div ref="arena" class="relative mx-auto h-48 w-full max-w-sm select-none">
            <button
                type="button"
                class="absolute left-[38%] top-1/2 rounded-full bg-primary px-10 py-4 text-lg font-semibold text-primary-foreground shadow-paper"
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
                class="absolute rounded-full border-2 border-foreground/20 bg-card px-7 py-3 font-semibold text-foreground/60 shadow-paper"
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

        <p class="mt-4 text-center font-hand text-lg text-foreground/40">
            <template v-if="dodgeCount > 4 && !noGone">it really doesn't want to be clicked</template>
            <template v-else-if="declineTaps > 0 && !noGone">{{ declineTaps }} tap{{ declineTaps > 1 ? 's' : '' }}, still no</template>
            <template v-else-if="noGone">that one's given up entirely</template>
        </p>
    </div>
</template>
