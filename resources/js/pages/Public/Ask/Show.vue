<script setup>
/**
 * Public 3-step page: /for-{slug}
 * Step count adapts to the ask: if it has no scheduling step, accepting on
 * step 1 jumps straight to step 3. If the ask was already answered before
 */
import { ref } from 'vue'
import WelcomeStep from '@/components/Ask/WelcomeStep.vue'
import SchedulingStep from '@/components/Ask/SchedulingStep.vue'
import FinalStep from '@/components/Ask/FinalStep.vue'
import BrandFooterCta from '@/components/Ask/BrandFooterCta.vue'

const props = defineProps({
    ask: { type: Object, required: true },
    slug: { type: String, required: true },
})

const step = ref(1)
const submitting = ref(false)
const declineTaps = ref(0)
const serverErrors = ref({})

function onAccepted(payload) {
    declineTaps.value = payload.declineTaps
    step.value = props.ask.has_scheduling_step ? 2 : 3
    if (!props.ask.has_scheduling_step) {
        saveResponse({})
    }
}

function onScheduleSubmit(payload) {
    saveResponse(payload)
}

async function saveResponse(payload) {
    submitting.value = true
    serverErrors.value = {}

    try {
        const res = await fetch(`/for-${props.slug}/respond`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                ...payload,
                decline_taps: declineTaps.value,
            }),
        })

        if (res.status === 422) {
            const data = await res.json()
            serverErrors.value = data.errors ?? {
                _general: data.message || 'Something about that submission was invalid.',
            }
            return
        }

        if (!res.ok) {
            serverErrors.value = {
                _general: "That didn't go through — check your connection and try again.",
            }
            return
        }

        step.value = 3
    } catch (err) {
        console.error(err)
        serverErrors.value = {
            _general: "That didn't go through — check your connection and try again.",
        }
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-background bg-dot-grid p-6">
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 opacity-[0.05] mix-blend-multiply">
            <svg width="100%" height="100%">
                <filter id="ask-grain">
                    <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch" />
                    <feColorMatrix type="saturate" values="0" />
                </filter>
                <rect width="100%" height="100%" filter="url(#ask-grain)" />
            </svg>
        </div>


        <div class="relative z-10 flex w-full flex-col items-center">
            <template>
                <WelcomeStep v-if="step === 1" :ask="ask" @accepted="onAccepted" />
                <SchedulingStep
                    v-else-if="step === 2"
                    :ask="ask"
                    :server-errors="serverErrors"
                    :submitting="submitting"
                    @submit="onScheduleSubmit"
                />
                <template v-else>
                    <FinalStep :ask="ask" />
                    <BrandFooterCta :creator-name="ask.creator_name" />
                </template>
            </template>
        </div>
    </div>
</template>
