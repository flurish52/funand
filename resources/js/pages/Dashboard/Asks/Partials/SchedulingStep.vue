<script setup>
import { computed } from 'vue'

const props = defineProps({
    form: { type: Object, required: true },
})

function addVenue() {
    props.form.venues.push({ name: '', address: '' })
}

function removeVenue(index) {
    props.form.venues.splice(index, 1)
}

function formatDate(date) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const formattedMinDate = computed(() => formatDate(props.form.min_date))
const formattedMaxDate = computed(() => formatDate(props.form.max_date))
</script>

<template>
    <section class="space-y-5">
        <label class="flex items-center gap-2.5">
            <input type="checkbox" v-model="form.has_scheduling_step" class="accent-primary w-4 h-4" />
            <span class="text-sm font-semibold text-foreground">Include a date/venue step</span>
        </label>
        <p v-if="!form.has_scheduling_step" class="font-hand text-base text-tertiary -mt-3">
            skipped — accepting will jump straight to the final message
        </p>

        <template v-if="form.has_scheduling_step">
            <div>
                <label class="block text-sm font-semibold text-foreground mb-1.5">Step heading</label>
                <input v-model="form.scheduling_heading" type="text" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                <p v-if="form.errors.scheduling_heading" class="text-destructive text-sm mt-1">{{ form.errors.scheduling_heading }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-foreground mb-1.5">Earliest date</label>
                    <input v-model="form.min_date" type="date" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                    <p v-if="form.errors.min_date" class="text-destructive text-sm mt-1">{{ form.errors.min_date }}</p>
                    <p v-else-if="formattedMinDate" class="font-hand text-base text-tertiary mt-1">from {{ formattedMinDate }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-foreground mb-1.5">Latest date</label>
                    <input v-model="form.max_date" type="date" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                    <p v-if="form.errors.max_date" class="text-destructive text-sm mt-1">{{ form.errors.max_date }}</p>
                    <p v-else-if="formattedMaxDate" class="font-hand text-base text-tertiary mt-1">till {{ formattedMaxDate }}</p>
                </div>
            </div>

            <div>
                <div class="flex items-baseline justify-between mb-2">
                    <label class="block text-sm font-semibold text-foreground">Venue options</label>
                    <span class="text-xs text-foreground/45">optional</span>
                </div>
                <div
                    v-for="(venue, i) in form.venues"
                    :key="i"
                    class="mb-3 rounded-lg border border-border/60 bg-background/20 p-3 sm:border-0 sm:bg-transparent sm:p-0"
                >
                    <div class="flex flex-col sm:flex-row sm:gap-2">
                        <div class="flex gap-2">
                            <input v-model="venue.name" type="text" placeholder="Venue name" class="flex-1 min-w-0 rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                            <button
                                type="button"
                                class="shrink-0 flex items-center justify-center w-11 h-11 rounded-lg border-1.5 border-border text-destructive hover:bg-destructive/10 transition-colors"
                                aria-label="Remove venue"
                                @click="removeVenue(i)"
                            >
                                ✕
                            </button>
                        </div>
                        <input v-model="venue.address" type="text" placeholder="Address (optional)" class="mt-2 sm:mt-0 flex-1 min-w-0 rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                        <button
                            type="button"
                            class="hidden shrink-0 items-center justify-center w-11 h-11 rounded-lg border-1.5 border-border text-destructive hover:bg-destructive/10 transition-colors"
                            aria-label="Remove venue"
                            @click="removeVenue(i)"
                        >
                            ✕
                        </button>
                    </div>
                    <p v-if="form.errors[`venues.${i}.name`]" class="text-destructive text-sm mt-1">{{ form.errors[`venues.${i}.name`] }}</p>
                    <p v-if="form.errors[`venues.${i}.address`]" class="text-destructive text-sm mt-1">{{ form.errors[`venues.${i}.address`] }}</p>
                </div>
                <button type="button" class="font-hand text-lg text-primary hover:opacity-70" @click="addVenue">+ add another venue</button>
            </div>
        </template>
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
