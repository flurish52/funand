<script setup>
/**
 * Create/Edit form for an ask. Mirrors the public 3-step flow exactly:
 * Step 1 = Welcome/Request, Step 2 = Date/Venue, Step 3 = Final message + publish.
 */
import { ref, computed } from 'vue'
import {Link, useForm} from '@inertiajs/vue3'
import { Check } from '@lucide/vue'
import Spinner from '@/components/Spinner.vue'
import WelcomeStep from './partials/WelcomeStep.vue'
import SchedulingStep from './partials/SchedulingStep.vue'
import FinalStep from './partials/FinalStep.vue'

const props = defineProps({
    ask: { type: Object, default: null },
})

const isEditing = computed(() => !!props.ask)
const activeTab = ref(1)

function toInputDate(date) {
    if (!date) return ''
    const d = new Date(date)
    if (Number.isNaN(d.getTime())) return ''
    return d.toISOString().slice(0, 10)
}

const form = useForm({
    for_name: props.ask?.for_name ?? '',
    type: props.ask?.type ?? 'custom',

    cover_image: null,
    question: props.ask?.question ?? 'Would you go on a date with me?',
    accept_label: props.ask?.accept_label ?? 'Yes',
    decline_label: props.ask?.decline_label ?? 'No',

    has_scheduling_step: props.ask?.has_scheduling_step ?? true,
    scheduling_heading: props.ask?.scheduling_heading ?? 'Pick a date & place',
    min_date: toInputDate(props.ask?.min_date),
    max_date: toInputDate(props.ask?.max_date),
    venues: props.ask?.venues?.map(v => ({ name: v.name, address: v.address })) ?? [{ name: '', address: '' }],

    final_heading: props.ask?.final_heading ?? 'Hurray!',
    final_message: props.ask?.final_message ?? 'I knew you would say yes!',

    share_text: props.ask?.share_text ?? '',
    is_published: props.ask?.is_published ?? false,
})

// Tracked here (not inside WelcomeStep) because submit() needs it when
// building the request payload — see the transform() below.
const removeCoverImage = ref(false)

const tabs = [
    { n: 1, label: 'Welcome' },
    { n: 2, label: 'Date & venue' },
    { n: 3, label: 'Final message' },
]

const tabFieldPrefixes = {
    1: ['for_name', 'type', 'cover_image', 'question', 'accept_label', 'decline_label'],
    2: ['scheduling_heading', 'min_date', 'max_date', 'venues'],
    3: ['final_heading', 'final_message'],
}

function fieldBelongsToTab(key, n) {
    return tabFieldPrefixes[n].some((p) => key === p || key.startsWith(p + '.'))
}

function tabHasError(n) {
    return Object.keys(form.errors).some((key) => fieldBelongsToTab(key, n))
}

const erroredTabs = computed(() => tabs.filter((t) => tabHasError(t.n)))
const hasAnyErrors = computed(() => Object.keys(form.errors).length > 0)

function jumpToFirstErrorTab(errors) {
    const keys = Object.keys(errors)
    for (const t of tabs) {
        if (keys.some((k) => fieldBelongsToTab(k, t.n))) {
            activeTab.value = t.n
            return
        }
    }
}

function goToTab(n) {
    activeTab.value = n
}

function nextStep() {
    if (activeTab.value < 3) activeTab.value += 1
}

function prevStep() {
    if (activeTab.value > 1) activeTab.value -= 1
}

function submit() {
    const options = { onError: jumpToFirstErrorTab }

    if (isEditing.value) {
        form
            .transform((data) => {
                const payload = { ...data, _method: 'put' }
                if (removeCoverImage.value) {
                    // explicit signal to actually clear the saved image —
                    // distinct from "key absent" below, which means "don't
                    // touch it"
                    payload.cover_image = null
                    payload.remove_cover_image = true
                } else if (!(payload.cover_image instanceof File)) {
                    delete payload.cover_image
                }
                return payload
            })
            .post(`/asks/${props.ask.id}`, options)
    } else {
        form.post('/asks', options)
    }
}
</script>

<template>
    <div class="max-w-xl mx-auto p-6">
        <div class="mb-8">
            <Link
                :href="`/asks`"
                class="group inline-flex items-center gap-2 mb-5 rounded-lg px-2 py-1.5 -ml-2 text-sm font-semibold text-muted-foreground transition-all hover:bg-muted hover:text-foreground"
            >
                <svg
                    aria-hidden="true"
                    class="h-4 w-4 transition-transform group-hover:-translate-x-0.5"
                    viewBox="0 0 20 20"
                    fill="none"
                >
                    <path
                        d="M15 10H5M9 14L5 10L9 6"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>

                <span>Back to asks</span>
            </Link>

            <div class="flex items-center gap-3">
                <svg
                    aria-hidden="true"
                    class="shrink-0 animate-bob"
                    width="24"
                    height="24"
                    viewBox="0 0 22 22"
                >
                    <circle cx="11" cy="9" r="7" class="fill-primary" />
                    <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
                    <line
                        x1="11"
                        y1="15"
                        x2="11"
                        y2="21"
                        stroke="#2B2320"
                        stroke-opacity="0.25"
                        stroke-width="1.5"
                        stroke-linecap="round"
                    />
                </svg>

                <h1 class="font-display font-bold text-2xl text-foreground">
                    {{ isEditing ? 'Edit ask' : 'New ask' }}
                </h1>
            </div>
        </div>
        <p class="text-sm text-foreground/55 mb-6 ml-9">
            {{ isEditing ? "Update anything below — it saves when you finish the last step." : "Three quick steps and your link is ready to send." }}
        </p>
        <!-- Step progress -->
        <div class="flex items-center justify-center gap-2 mb-7">
            <template v-for="(t, idx) in tabs" :key="t.n">
                <button type="button" class="group flex items-center gap-2" @click="goToTab(t.n)">
                    <span
                        class="relative flex h-8 w-8 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold transition-colors"
                        :class="[
                            activeTab === t.n ? 'border-primary bg-primary text-primary-foreground' :
                            t.n < activeTab ? 'border-tertiary/60 bg-tertiary/10 text-tertiary' :
                            'border-border bg-background text-foreground/35',
                            tabHasError(t.n) && 'border-destructive text-destructive',
                        ]"
                    >
                        <Check v-if="t.n < activeTab && !tabHasError(t.n)" class="size-4" />
                        <template v-else>{{ t.n }}</template>
                        <span
                            v-if="tabHasError(t.n)"
                            aria-hidden="true"
                            class="absolute -top-1 -right-1 h-2.5 w-2.5 rounded-full bg-destructive ring-2 ring-background"
                        ></span>
                    </span>
                    <span
                        class="hidden text-sm font-medium sm:inline transition-colors"
                        :class="activeTab === t.n ? 'text-foreground' : 'text-foreground/45 group-hover:text-foreground/70'"
                    >
                        {{ t.label }}
                    </span>
                </button>
                <div v-if="idx < tabs.length - 1" class="h-px w-6 sm:w-10 bg-border shrink-0"></div>
            </template>
        </div>

        <div
            v-if="hasAnyErrors"
            role="alert"
            aria-live="polite"
            class="mb-5 rounded-lg border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"
        >
            Something needs fixing in
            <span class="font-semibold">{{ erroredTabs.map((t) => t.label).join(', ') }}</span>
            — check the step{{ erroredTabs.length > 1 ? 's' : '' }} above.
        </div>

        <form @submit.prevent="submit">
            <div class="rounded-xl border border-border bg-card shadow-paper p-6">
                <WelcomeStep
                    v-show="activeTab === 1"
                    :form="form"
                    :ask="ask"
                    v-model:remove-cover-image="removeCoverImage"
                />
                <SchedulingStep v-show="activeTab === 2" :form="form" />
                <FinalStep v-show="activeTab === 3" :form="form" :ask="ask" />
            </div>

            <!-- Guided footer: back / continue / save, always in the same place -->
            <div class="mt-5 flex items-center justify-between gap-3">
                <button
                    v-if="activeTab > 1"
                    type="button"
                    class="rounded-full border border-border px-5 py-2.5 text-sm font-semibold text-foreground/70 hover:text-foreground hover:border-foreground/30 transition-colors"
                    @click="prevStep"
                >
                    Back
                </button>
                <span v-else></span>

                <button
                    v-if="activeTab < 3"
                    type="button"
                    class="rounded-full bg-foreground text-background px-6 py-2.5 text-sm font-semibold hover:-translate-y-0.5 transition-all duration-200"
                    @click="nextStep"
                >
                    Continue
                </button>
                <button
                    v-else
                    type="submit"
                    :disabled="form.processing"
                    class="flex items-center gap-2 rounded-full bg-primary text-primary-foreground px-6 py-2.5 text-sm font-semibold shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200 disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:rotate-0"
                >
                    <Spinner v-if="form.processing" class="size-4" />
                    {{ isEditing ? 'Save changes' : 'Create ask' }}
                </button>
            </div>
        </form>
    </div>
</template>
