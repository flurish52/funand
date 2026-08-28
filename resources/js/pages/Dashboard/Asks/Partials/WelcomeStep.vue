<script setup>
import { ref, computed, onBeforeUnmount } from 'vue'
import { useImageCompression } from '@/composables/useImageCompression'

const props = defineProps({
    form: { type: Object, required: true },
    ask: { type: Object, default: null },
    removeCoverImage: { type: Boolean, default: false },
})

const emit = defineEmits(['update:removeCoverImage'])

const isEditing = computed(() => !!props.ask)
const { compressImage } = useImageCompression()

function resolveCoverUrl(path) {
    if (!path) return null
    if (/^https?:\/\//.test(path) || path.startsWith('/')) return path
    return `/storage/${path}`
}

const coverPreviewUrl = ref(resolveCoverUrl(props.ask?.cover_image))
const showCoverPicker = ref(!!coverPreviewUrl.value)
const compressingCover = ref(false)
const fileInput = ref(null)
let objectUrl = null

async function onCoverChange(e) {
    const file = e.target.files[0] ?? null
    emit('update:removeCoverImage', false)

    if (objectUrl) {
        URL.revokeObjectURL(objectUrl)
        objectUrl = null
    }

    if (!file) {
        props.form.cover_image = null
        coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
        return
    }

    compressingCover.value = true
    try {
        props.form.cover_image = await compressImage(file)
    } finally {
        compressingCover.value = false
    }

    objectUrl = URL.createObjectURL(props.form.cover_image)
    coverPreviewUrl.value = objectUrl
}

// The × button does one of two things depending on what's showing:
//  - a newly-picked file is staged  → cancel it, revert to the saved image
//  - the saved image itself is showing → mark it for removal on save
function clearCoverImage() {
    if (props.form.cover_image) {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl)
            objectUrl = null
        }
        props.form.cover_image = null
        coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
    } else if (coverPreviewUrl.value) {
        emit('update:removeCoverImage', true)
        coverPreviewUrl.value = null
        showCoverPicker.value = false
    }
    if (fileInput.value) fileInput.value.value = ''
}

function undoRemoveCoverImage() {
    emit('update:removeCoverImage', false)
    coverPreviewUrl.value = resolveCoverUrl(props.ask?.cover_image)
    showCoverPicker.value = true
}

onBeforeUnmount(() => {
    if (objectUrl) URL.revokeObjectURL(objectUrl)
})
</script>

<template>
    <section class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-foreground mb-1.5">Who is this for?</label>
            <input
                v-model="form.for_name"
                type="text"
                placeholder="e.g. my friends, my crush, my babe"
                class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"
                :class="{ 'shake border-destructive ring-2 ring-destructive/15': form.errors.for_name }"
            />
            <p v-if="form.errors.for_name" class="text-destructive text-sm mt-1">{{ form.errors.for_name }}</p>
        </div>

        <div>
            <label class="block text-sm font-semibold text-foreground mb-1.5">Type</label>
            <select v-model="form.type" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow">
                <option value="date">Date</option>
                <option value="money">Money</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-foreground mb-1.5">Question</label>
            <input
                v-model="form.question"
                type="text"
                placeholder="Would you go on a date with me?"
                class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow"
                :class="{ 'shake border-destructive ring-2 ring-destructive/15': form.errors.question }"
            />
            <p v-if="form.errors.question" class="text-destructive text-sm mt-1">{{ form.errors.question }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm font-semibold text-foreground mb-1.5">Accept button</label>
                <input v-model="form.accept_label" type="text" placeholder="Yes" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                <p v-if="form.errors.accept_label" class="text-destructive text-sm mt-1">{{ form.errors.accept_label }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-foreground mb-1.5">Decline text</label>
                <input v-model="form.decline_label" type="text" placeholder="No" class="w-full rounded-lg border-1.5 border-border bg-background/40 h-11 px-3.5 focus:border-tertiary focus:ring-2 focus:ring-tertiary/15 outline-none transition-shadow" />
                <p v-if="form.errors.decline_label" class="text-destructive text-sm mt-1">{{ form.errors.decline_label }}</p>
            </div>
        </div>
        <p class="-mt-3 text-xs text-foreground/45">Decline is always shown but never clickable — it just nudges toward yes.</p>

        <!-- Cover image, tucked behind a toggle so step 1 doesn't front-load an optional field -->
        <div class="pt-1 border-t border-border/60">
            <button
                v-if="!showCoverPicker && !coverPreviewUrl"
                type="button"
                class="mt-4 font-hand text-lg text-primary hover:opacity-70"
                @click="showCoverPicker = true"
            >
                + add a cover image (optional)
            </button>

            <div v-else class="mt-4">
                <label class="block text-sm font-semibold text-foreground mb-1.5">Cover image</label>
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
                        <p v-if="compressingCover" class="text-xs text-foreground/45 mt-1">Compressing image…</p>
                    </div>
                </div>
                <p v-if="form.errors.cover_image" class="text-destructive text-sm mt-1">{{ form.errors.cover_image }}</p>
            </div>
        </div>
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
