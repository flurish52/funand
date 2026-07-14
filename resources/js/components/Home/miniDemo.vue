<script setup>
/**
 * Live, working preview of the core mechanic — the hero's signature
 * moment. The "No" button now actually dodges the cursor on desktop
 * (continuous mousemove repulsion, same physics as WelcomeStep.vue),
 * and shrinks-to-nothing on tap for touch devices, where there's no
 * cursor to dodge with in the first place.
 */
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import Confetti from './confetti.vue'
import Sticker from './sticker.vue'
import Tape from './tape.vue'
import Pin from './pin.vue'

const arena = ref(null)
const noBtn = ref(null)

const offset = reactive({ x: 0, y: 0 })
const taps = ref(0)
const confetti = ref(null)
const accepted = ref(false)
const dodgeCount = ref(0)

const noScale = computed(() => Math.max(0, 1 - taps.value * 0.16))
const noGone = computed(() => noScale.value <= 0.08)

let rafPending = false
let lastEvent = null

function clamp(v, min, max) {
    return Math.min(max, Math.max(min, v))
}

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

    const SAFE_RADIUS = 110
    if (dist >= SAFE_RADIUS) return

    const angle = Math.atan2(dy, dx)
    const push = (SAFE_RADIUS - dist) * 1.1

    const maxX = (arenaRect.width - btnRect.width) / 2 - 4
    const maxY = (arenaRect.height - btnRect.height) / 2 - 4

    offset.x = clamp(offset.x + Math.cos(angle) * push, -maxX, maxX)
    offset.y = clamp(offset.y + Math.sin(angle) * push, -maxY, maxY)
    dodgeCount.value += 1
}

// Fallback for touch, or the rare lucky click: shrink a notch and hop.
function tapNo() {
    if (noGone.value) return
    taps.value += 1
    const jump = 20
    const a = Math.random() * Math.PI * 2
    offset.x = clamp(offset.x + Math.cos(a) * jump, -80, 80)
    offset.y = clamp(offset.y + Math.sin(a) * jump, -40, 40)
}

function tapYes() {
    accepted.value = true
    confetti.value?.burst()
    setTimeout(() => (accepted.value = false), 1200)
}

onMounted(() => {
    window.addEventListener('mousemove', handlePointerMove, { passive: true })
})
onUnmounted(() => {
    window.removeEventListener('mousemove', handlePointerMove)
})
</script>

<template>
    <div class="relative rotate-2">
        <Sticker text="2-min setup" class-name="absolute -top-5 -right-5 rotate-6 z-10" />
        <Tape color="secondary" width="6rem" rotate="-6deg" class-name="-top-3 left-8 z-10" />
        <Tape color="primary" width="5rem" rotate="8deg" class-name="-bottom-3 right-10 z-10" />
        <Pin color="tertiary" class-name="-top-2 left-1/2 -translate-x-1/2 z-10" />

        <div class="w-full max-w-sm rounded-lg edge-torn border border-border/70 p-7 pt-9 bg-card shadow-paper">
            <p class="font-hand text-xl text-tertiary mb-1 -rotate-1">someone sends this ↓</p>
            <p class="font-display font-semibold text-xl text-foreground mb-6 leading-snug">
                Would you pay for my lunch today?
            </p>

            <div ref="arena" class="relative h-28 w-full select-none">
                <button
                    type="button"
                    class="absolute left-[36%] top-1/2 -translate-x-1/2 -translate-y-1/2 rounded-md bg-primary px-8 py-3 font-semibold text-primary-foreground shadow-paper transition-transform duration-200 hover:-translate-y-[calc(50%+3px)] active:scale-95"
                    @click="tapYes"
                >
                    Yes 😊
                </button>

                <button
                    v-if="!noGone"
                    ref="noBtn"
                    type="button"
                    class="absolute rounded-md border-2 border-foreground/15 bg-card px-6 py-3 font-semibold text-foreground/60 shadow-paper"
                    :style="{
            left: '64%',
            top: '50%',
            transform: `translate(calc(-50% + ${offset.x}px), calc(-50% + ${offset.y}px)) scale(${noScale})`,
            transition: 'transform 90ms ease-out',
          }"
                    @click="tapNo"
                >
                    No😥
                </button>

                <div class="pointer-events-none absolute left-1/2 top-0">
                    <Confetti ref="confetti" />
                </div>
            </div>

            <p class="text-xs text-muted-foreground mt-4 h-4">
                <template v-if="accepted">that's the whole idea 🎉</template>
                <template v-else-if="dodgeCount > 4 && !noGone">it's not going to let you</template>
                <template v-else-if="taps === 0">go on, try to tap "No" — good luck</template>
                <template v-else-if="!noGone">nice try. {{ taps }} tap{{ taps > 1 ? 's' : '' }} and counting</template>
                <template v-else>it gave up entirely</template>
            </p>
        </div>
    </div>
</template>
