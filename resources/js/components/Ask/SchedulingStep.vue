<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    ask: { type: Object, required: true },
    serverErrors: { type: Object, default: () => ({}) },
    submitting: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const chosenDate = ref('')
const chosenTime = ref('')
const chosenVenueId = ref(props.ask.venues?.[0]?.id ?? null)
const localErrors = ref({})

// Server errors come back keyed by the Laravel field name (responded_date,
// responded_time, ask_venue_id) with an array of messages each. Merge them
// with local errors so both sources render the same way.
function fieldError(key) {
    if (localErrors.value[key]) return localErrors.value[key]
    if (props.serverErrors?.[key]?.length) return props.serverErrors[key][0]
    return null
}

// Clear a field's error as soon as the person starts fixing it.
watch(chosenDate, () => delete localErrors.value.responded_date)
watch(chosenTime, () => delete localErrors.value.responded_time)

function submit() {
    localErrors.value = {}
    if (!chosenDate.value) localErrors.value.responded_date = 'Pick a date'
    if (!chosenTime.value) localErrors.value.responded_time = 'Pick a time'
    if (Object.keys(localErrors.value).length) return

    emit('submit', {
        responded_date: chosenDate.value,
        responded_time: chosenTime.value,
        ask_venue_id: chosenVenueId.value,
    })
}
</script>

<template>
    <div class="w-full max-w-md rounded-lg edge-torn border border-border bg-card p-8 shadow-paper">
        <h2 class="mb-6 text-center font-display text-2xl font-bold text-foreground">
            {{ ask.scheduling_heading || 'Pick a date & place' }}
        </h2>

        <!-- General, non-field-specific server error (e.g. already answered, network failure) -->
        <div
            v-if="serverErrors?._general"
            class="mb-5 rounded-lg border border-destructive/30 bg-destructive/10 p-3 text-sm text-destructive"
        >
            {{ serverErrors._general }}
        </div>

        <div class="mb-5 space-y-3">
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-foreground">
                    When works? <span class="text-destructive">*</span>
                </label>
                <input
                    v-model="chosenDate"
                    type="date"
                    :min="ask.min_date"
                    :max="ask.max_date"
                    class="w-full rounded-lg border-1.5 bg-background/40 p-2.5 outline-none transition-shadow focus:ring-2 focus:ring-tertiary/15"
                    :class="fieldError('responded_date') ? 'border-destructive' : 'border-border focus:border-tertiary'"
                    :aria-invalid="!!fieldError('responded_date')"
                />
                <p v-if="fieldError('responded_date')" class="mt-1 text-xs font-medium text-destructive">
                    {{ fieldError('responded_date') }}
                </p>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-foreground">
                    What time? <span class="text-destructive">*</span>
                </label>
                <input
                    v-model="chosenTime"
                    type="time"
                    class="w-full min-w-0 rounded-lg border-1.5 bg-background/40 p-2.5 text-base outline-none transition-shadow focus:ring-2 focus:ring-tertiary/15"
                    :class="fieldError('responded_time') ? 'border-destructive' : 'border-border focus:border-tertiary'"
                    :aria-invalid="!!fieldError('responded_time')"
                />
                <p v-if="fieldError('responded_time')" class="mt-1 text-xs font-medium text-destructive">
                    {{ fieldError('responded_time') }}
                </p>
            </div>
        </div>

        <div v-if="ask.venues?.length" class="mb-2">
            <label class="mb-1.5 block text-sm font-semibold text-foreground">Where?</label>
            <div class="space-y-2">
                <label
                    v-for="venue in ask.venues"
                    :key="venue.id"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border-1.5 p-3 transition-colors"
                    :class="chosenVenueId === venue.id ? 'border-tertiary bg-tertiary/10' : 'border-border'"
                >
                    <input type="radio" v-model="chosenVenueId" :value="venue.id" class="accent-tertiary" />
                    <span>
            <span class="block text-sm font-semibold text-foreground">{{ venue.name }}</span>
            <span v-if="venue.address" class="block text-xs text-foreground/50">{{ venue.address }}</span>
          </span>
                </label>
            </div>
            <p v-if="fieldError('ask_venue_id')" class="mt-1 text-xs font-medium text-destructive">
                {{ fieldError('ask_venue_id') }}
            </p>
        </div>

        <button
            type="button"
            :disabled="submitting"
            class="mt-4 w-full rounded-full bg-primary py-3.5 font-semibold text-primary-foreground shadow-paper transition-all duration-200 hover:-translate-y-0.5 hover:rotate-1 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0 disabled:hover:rotate-0"
            @click="submit"
        >
            {{ submitting ? 'Confirming…' : 'Confirm' }}
        </button>
    </div>
</template>
