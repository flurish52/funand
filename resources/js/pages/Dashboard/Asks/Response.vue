<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
    asks: { type: Array, required: true },
})

const selected = ref(null)

const statusStyles = {
    pending: 'bg-gray-100 text-gray-500',
    opened: 'bg-amber-100 text-amber-700',
    answered: 'bg-[#F5C4B3] text-[#4A1B0C]',
}

const statusLabel = {
    pending: 'not opened',
    opened: 'deciding…',
    answered: 'answered',
}

function open(ask) {
    selected.value = ask
}

function close() {
    selected.value = null
}

const timeToYes = computed(() => {
    const s = selected.value?.response?.seconds_to_yes
    if (!s && s !== 0) return null
    if (s < 60) return `${s}s`
    return `${Math.floor(s / 60)}m ${s % 60}s`
})

function copyLink(ask) {
    if (ask.share_url) navigator.clipboard.writeText(ask.share_url)
}
</script>

<template>
    <Head title="Responses" />

    <div class="min-h-screen bg-[#FAECE7] px-4 py-10">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-xl font-semibold text-[#712B13] mb-6">your asks</h1>

            <div v-if="asks.length === 0" class="bg-white rounded-2xl border border-[#F0997B]/30 p-8 text-center text-sm text-gray-500">
                you haven't created any asks yet.
            </div>

            <div v-else class="bg-white rounded-2xl border border-[#F0997B]/30 divide-y divide-[#F0997B]/15 overflow-hidden">
                <div
                    v-for="ask in asks"
                    :key="ask.id"
                    class="flex items-center justify-between gap-4 px-5 py-4"
                >
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ ask.for_name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ ask.question }}</p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
            <span
                class="text-xs font-medium px-3 py-1 rounded-full"
                :class="statusStyles[ask.status]"
            >
              {{ statusLabel[ask.status] }}
            </span>
                        <button
                            @click="open(ask)"
                            class="text-sm font-medium text-[#712B13] border border-[#F0997B]/40 rounded-lg px-3 py-1.5 hover:bg-[#FAECE7] transition"
                        >
                            view
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="selected"
            class="fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-50"
            @click.self="close"
        >
            <div class="bg-white rounded-2xl w-full max-w-md p-6 relative">
                <button
                    @click="close"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-sm"
                    aria-label="close"
                >
                    ✕
                </button>

                <p class="text-xs text-gray-400 mb-1">{{ selected.for_name }}</p>
                <p class="text-lg font-medium mb-6 pr-6">{{ selected.question }}</p>

                <!-- Not opened -->
                <div v-if="selected.status === 'pending'" class="text-center py-6">
                    <p class="text-sm text-gray-500 mb-4">nobody's opened this link yet.</p>
                    <button
                        v-if="selected.share_url"
                        @click="copyLink(selected)"
                        class="w-full border border-[#F0997B]/40 rounded-lg py-2 text-sm font-medium text-[#712B13] hover:bg-[#FAECE7] transition"
                    >
                        copy link
                    </button>
                </div>

                <!-- Opened, still deciding -->
                <div v-else-if="selected.status === 'opened'" class="text-center py-6">
                    <p class="text-sm text-gray-500 mb-2">they've opened it and are still deciding.</p>
                    <p class="text-3xl font-semibold text-[#993C1D] mb-1">{{ selected.response.decline_taps }}</p>
                    <p class="text-xs text-gray-400">
                        times they've tapped "{{ selected.decline_label }}" so far
                    </p>
                </div>

                <!-- Answered -->
                <div v-else class="text-center py-2">
                    <div class="w-16 h-16 rounded-full bg-[#F0997B] flex items-center justify-center mx-auto mb-3">
                        <span class="text-2xl text-[#4A1B0C]">&#10003;</span>
                    </div>
                    <p class="text-2xl font-semibold text-[#712B13] mb-1">
                        {{ selected.for_name }} said {{ selected.accept_label.toLowerCase() }}
                    </p>
                    <p class="text-xs text-gray-400 mb-5">
                        {{ new Date(selected.response.responded_at).toLocaleString() }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 text-left mb-4">
                        <div class="bg-[#FAECE7] rounded-lg p-3">
                            <p class="text-xs text-gray-400 mb-1">decline taps</p>
                            <p class="text-xl font-semibold">{{ selected.response.decline_taps }}</p>
                        </div>
                        <div class="bg-[#FAECE7] rounded-lg p-3">
                            <p class="text-xs text-gray-400 mb-1">time to yes</p>
                            <p class="text-xl font-semibold">{{ timeToYes ?? '—' }}</p>
                        </div>
                    </div>

                    <div
                        v-if="selected.has_scheduling_step && selected.response.responded_date"
                        class="text-left bg-[#FAECE7] rounded-lg p-3"
                    >
                        <p class="text-xs text-gray-400 mb-1">picked</p>
                        <p class="text-sm font-medium">
                            {{ selected.response.responded_date }} at {{ selected.response.responded_time }}
                        </p>
                        <p v-if="selected.response.venue" class="text-sm text-gray-600 mt-1">
                            {{ selected.response.venue.name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
