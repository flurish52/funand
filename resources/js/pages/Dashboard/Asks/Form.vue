<script setup>
/**
 * Create/Edit form for an ask. Mirrors the public 3-step flow exactly:
 * Tab 1 = Welcome/Request, Tab 2 = Date/Venue, Tab 3 = Final message.
 * Plus link + publish settings at the bottom.
 */
import { ref, computed, onBeforeUnmount } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    ask: { type: Object, default: null },
})

const isEditing = computed(() => !!props.ask)
const activeTab = ref(1)

// Native <input type="date"> only accepts/reflects "YYYY-MM-DD". If the
// backend sends a full timestamp (or anything else Date can parse), this
// normalizes it so the field actually shows the existing value instead of
// rendering blank while the real value silently sits in form state.
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

const tabs = [
    { n: 1, label: 'Welcome' },
    { n: 2, label: 'Date & venue' },
    { n: 3, label: 'Final message' },
]

// --- Cross-tab error surfacing --------------------------------------
// Validation errors come back keyed by field name with no idea which
// tab they belong to. Without this, a step-3 error is invisible if the
// user is looking at step 1 — the form just silently "fails" from
// their point of view. This maps each field to its tab, then (a) jumps
// to the first tab with an error on submit failure, and (b) badges
// every tab that has one so it's still visible after they navigate away.
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

function addVenue() {
    form.venues.push({ name: '', address: '' })
}

function removeVenue(index) {
    form.venues.splice(index, 1)
}

// --- Cover image: preview, cancel, and explicit removal --------------
function resolveCoverUrl(path) {
    if (!path) return null
    if (/^https?:\/\//.test(path) || path.startsWith('/')) return path
    return `/storage/${path}`
}

const coverPreviewUrl = ref(resolveCoverUrl(props.ask?.cover_image))
const removeCoverImage = ref(false)
const fileInput = ref(null)
let objectUrl = null

function onCoverChange(e) {
    const file = e.target.files[0] ?? null
    form.cover_image = file
    removeCoverImage.value = false

    if (objectUrl) {
        URL.revokeObjectURL(objectUrl)
        objectUrl = null
    }

    if (file) {
        objectUrl = URL.createObjectURL(file)
        coverPreviewUrl.value = objectUrl
    } else {
        coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
    }
}

// The × button does one of two things depending on what's showing:
//  - a newly-picked file is staged  → cancel it, revert to the saved image
//  - the saved image itself is showing → mark it for removal on save
function clearCoverImage() {
    if (form.cover_image) {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl)
            objectUrl = null
        }
        form.cover_image = null
        coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
    } else if (coverPreviewUrl.value) {
        removeCoverImage.value = true
        coverPreviewUrl.value = null
    }
    if (fileInput.value) fileInput.value.value = ''
}

function undoRemoveCoverImage() {
    removeCoverImage.value = false
    coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
}

onBeforeUnmount(() => {
    if (objectUrl) URL.revokeObjectURL(objectUrl)
})

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

const formatDate = (date) => {
    if (!date) return ''

    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const formattedMinDate = computed(() => formatDate(form.min_date))
const formattedMaxDate = computed(() => formatDate(form.max_date))

// The link is generated by the backend when the ask is first created —
// it's not something typed here, just displayed once it exists.
const origin = typeof window !== 'undefined' ? window.location.origin : ''
const fullLink = computed(() => (props.ask?.slug ? `${origin}/for-${props.ask.slug}` : ''))
const justCopied = ref(false)

function copyLink() {
    if (!fullLink.value) return
    navigator.clipboard.writeText(fullLink.value)
    justCopied.value = true
    setTimeout(() => (justCopied.value = false), 1600)
}
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <div class="flex items-center gap-3 mb-6">
            <svg aria-hidden="true" class="shrink-0 animate-bob" width="26" height="26" viewBox="0 0 22 22">
                <circle cx="11" cy="9" r="7" class="fill-primary" />
                <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
                <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5" stroke-linecap="round" />
            </svg>
            <h1 class="font-display font-bold text-2xl text-foreground">
                {{ isEditing ? 'Edit ask' : 'New ask' }}
            </h1>
        </div>

        <div
            v-if="hasAnyErrors"
            role="alert"
            aria-live="polite"
            class="mb-4 rounded-lg border border-destructive/30 bg-destructive/10 px-4 py-3 text-sm font-medium text-destructive"
        >
            Something needs fixing in
            <span class="font-semibold">{{ erroredTabs.map((t) => t.label).join(', ') }}</span>
            — check the tab{{ erroredTabs.length > 1 ? 's' : '' }} marked below.
        </div>

        <!-- Folder tabs — a real 3-step sequence, so numbering stays -->
        <div class="flex gap-1 mb-6">
            <button
                v-for="t in tabs"
                :key="t.n"
                type="button"
                class="relative px-4 py-2.5 text-sm font-semibold rounded-t-lg border border-b-0 transition-colors"
                :class="activeTab === t.n
          ? 'bg-card border-border text-primary z-10 -mb-px'
          : 'bg-muted/60 border-transparent text-foreground/50 hover:text-foreground/70'"
                @click="activeTab = t.n"
            >
                <span class="text-secondary font-display mr-1">{{ t.n }}</span>{{ t.label }}
                <span
                    v-if="tabHasError(t.n)"
                    aria-hidden="true"
                    class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-destructive ring-2 ring-background"
                ></span>
            </button>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="rounded-lg rounded-tl-none border border-border bg-card shadow-paper p-6 -mt-6 pt-10">
                <!-- Step 1 -->
                <section v-show="activeTab === 1" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">Who is this for?</label>
                        <input
                            v-model="form.for_name"
                            type="text"
                            placeholder="e.g. My Crush, Agnes"
                            class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"
                            :class="{ 'shake border-destructive ring-2 ring-destructive/15': form.errors.for_name }"
                        />
                        <p v-if="form.errors.for_name" class="text-destructive text-sm mt-1">{{ form.errors.for_name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">Type</label>
                        <select v-model="form.type" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow">
                            <option value="date">Date</option>
                            <option value="money">Money</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">
                            {{ coverPreviewUrl ? 'Cover image' : 'Add a cover image' }}
                        </label>

                        <div class="flex items-center gap-4">
                            <div v-if="coverPreviewUrl" class="relative shrink-0">
                                <div class="w-16 h-16 rounded-md overflow-hidden border border-border bg-muted">
                                    <img :src="coverPreviewUrl" alt="Cover preview" class="w-full h-full object-cover" />
                                </div>
                                <button
                                    type="button"
                                    class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-foreground text-background text-[10px] flex items-center justify-center shadow-paper hover:scale-110 transition-transform"
                                    :aria-label="form.cover_image ? 'cancel new image' : 'remove image'"
                                    @click="clearCoverImage"
                                >
                                    ✕
                                </button>
                            </div>

                            <div class="min-w-0">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    @change="onCoverChange"
                                    class="w-full text-sm text-foreground/70 file:mr-3 file:rounded-full file:border-0 file:bg-accent file:text-accent-foreground file:px-3 file:py-1.5 file:text-sm file:font-semibold"
                                />
                                <p v-if="removeCoverImage" class="font-hand text-base text-primary mt-1">
                                    image will be removed when you save —
                                    <button type="button" class="underline hover:opacity-70" @click="undoRemoveCoverImage">undo</button>
                                </p>
                                <p v-else-if="coverPreviewUrl && !form.cover_image && isEditing" class="font-hand text-base text-tertiary mt-1">
                                    current image kept unless you pick a new one
                                </p>
                            </div>
                        </div>
                        <p v-if="form.errors.cover_image" class="text-destructive text-sm mt-1">{{ form.errors.cover_image }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">Question</label>
                        <input
                            v-model="form.question"
                            type="text"
                            placeholder="Would you go on a date with me?"
                            class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"
                            :class="{ 'shake border-destructive ring-2 ring-destructive/15': form.errors.question }"
                        />
                        <p v-if="form.errors.question" class="text-destructive text-sm mt-1">{{ form.errors.question }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-foreground mb-1.5">Accept button text</label>
                            <input v-model="form.accept_label" type="text" placeholder="Yes / Now" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                            <p v-if="form.errors.accept_label" class="text-destructive text-sm mt-1">{{ form.errors.accept_label }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-foreground mb-1.5">Decline text (never clickable)</label>
                            <input v-model="form.decline_label" type="text" placeholder="No / I don't know" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                            <p v-if="form.errors.decline_label" class="text-destructive text-sm mt-1">{{ form.errors.decline_label }}</p>
                        </div>
                    </div>
                </section>

                <!-- Step 2 -->
                <section v-show="activeTab === 2" class="space-y-4">
                    <label class="flex items-center gap-2.5">
                        <input type="checkbox" v-model="form.has_scheduling_step" class="accent-primary w-4 h-4" />
                        <span class="text-sm font-semibold text-foreground">Include a date/venue step</span>
                    </label>

                    <template v-if="form.has_scheduling_step">
                        <div>
                            <label class="block text-sm font-semibold text-foreground mb-1.5">Step heading</label>
                            <input v-model="form.scheduling_heading" type="text" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                            <p v-if="form.errors.scheduling_heading" class="text-destructive text-sm mt-1">{{ form.errors.scheduling_heading }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-foreground mb-1.5">Earliest date</label>
                                <input v-model="form.min_date" type="date" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                                <p v-if="form.errors.min_date" class="text-destructive text-sm mt-1">{{ form.errors.min_date }}</p>
                                <p v-else-if="formattedMinDate" class="font-hand text-base text-tertiary mt-1">from {{ formattedMinDate }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-foreground mb-1.5">Latest date</label>
                                <input v-model="form.max_date" type="date" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                                <p v-if="form.errors.max_date" class="text-destructive text-sm mt-1">{{ form.errors.max_date }}</p>
                                <p v-else-if="formattedMaxDate" class="font-hand text-base text-tertiary mt-1">till {{ formattedMaxDate }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-foreground mb-2">Venue options (optional)</label>
                            <div v-for="(venue, i) in form.venues" :key="i" class="mb-2">
                                <div class="flex gap-2">
                                    <input v-model="venue.name" type="text" placeholder="Venue name" class="flex-1 rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                                    <input v-model="venue.address" type="text" placeholder="Address (optional)" class="flex-1 rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                                    <button type="button" class="text-destructive px-2 hover:opacity-70" @click="removeVenue(i)">✕</button>
                                </div>
                                <p v-if="form.errors[`venues.${i}.name`]" class="text-destructive text-sm mt-1">{{ form.errors[`venues.${i}.name`] }}</p>
                                <p v-if="form.errors[`venues.${i}.address`]" class="text-destructive text-sm mt-1">{{ form.errors[`venues.${i}.address`] }}</p>
                            </div>
                            <button type="button" class="font-hand text-lg text-primary hover:opacity-70" @click="addVenue">+ add another venue</button>
                        </div>
                    </template>
                </section>

                <!-- Step 3 -->
                <section v-show="activeTab === 3" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">Final heading</label>
                        <input v-model="form.final_heading" type="text" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                        <p v-if="form.errors.final_heading" class="text-destructive text-sm mt-1">{{ form.errors.final_heading }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-foreground mb-1.5">Final message</label>
                        <textarea v-model="form.final_message" rows="3" class="w-full rounded-lg border-1.5 border-border bg-background/40 p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"></textarea>
                        <p v-if="form.errors.final_message" class="text-destructive text-sm mt-1">{{ form.errors.final_message }}</p>
                    </div>
                </section>
            </div>

            <!-- Link + publish, always visible -->
            <section class="space-y-4 rounded-lg border border-border bg-accent/30 p-5">
                <div>
                    <label class="block text-sm font-semibold text-foreground mb-1.5">Your link</label>

                    <!-- Backend-generated once the ask is first saved — never
                         user-editable, so this is display-only. -->
                    <div
                        v-if="fullLink"
                        class="flex items-center justify-between gap-3 rounded-lg border-1.5 border-border bg-card p-2.5"
                    >
                        <span class="truncate text-sm text-foreground/80">{{ fullLink }}</span>
                        <button
                            type="button"
                            class="shrink-0 text-xs font-semibold rounded-full border border-border px-3 py-1 text-foreground/60 hover:border-tertiary hover:text-tertiary transition-colors"
                            @click="copyLink"
                        >
                            {{ justCopied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                    <p v-else class="font-hand text-lg text-tertiary">
                        your link gets created automatically once you save this ask
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-foreground mb-1.5">Share caption (optional)</label>
                    <input v-model="form.share_text" type="text" placeholder="Something to go along with the link when you share it" class="w-full rounded-lg border-1.5 border-border bg-card p-2.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                </div>

                <label class="flex items-center gap-2.5">
                    <input type="checkbox" v-model="form.is_published" class="accent-primary w-4 h-4" />
                    <span class="text-sm font-semibold text-foreground">Publish (make link live)</span>
                </label>
            </section>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-full bg-primary text-primary-foreground font-semibold py-3.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200 disabled:opacity-50 disabled:hover:translate-y-0 disabled:hover:rotate-0"
            >
                {{ isEditing ? 'Save changes' : 'Create ask' }}
            </button>
        </form>
    </div>
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
