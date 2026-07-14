<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])

function toggle() {
    if (props.disabled) return
    emit('update:modelValue', !props.modelValue)
}
</script>

<template>
    <button
        type="button"
        role="switch"
        :aria-checked="modelValue"
        :disabled="disabled"
        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-tertiary/30 disabled:cursor-not-allowed disabled:opacity-50"
        :class="modelValue ? 'bg-tertiary' : 'bg-border'"
        @click="toggle"
    >
        <span
            class="inline-block h-4.5 w-4.5 transform rounded-full bg-card shadow-paper transition-transform duration-200"
            :class="modelValue ? 'translate-x-[22px]' : 'translate-x-[3px]'"
        />
    </button>
</template>
