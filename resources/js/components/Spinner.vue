<script setup>
const props = defineProps({
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        class="size-4 animate-spin"
        :class="props.class"
        role="status"
        aria-label="Loading"
    >
        <circle
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="3"
            stroke-opacity="0.25"
        />
        <path
            d="M22 12a10 10 0 0 1-10 10"
            stroke="currentColor"
            stroke-width="3"
            stroke-linecap="round"
        />
    </svg>
</template>
