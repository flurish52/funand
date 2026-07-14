<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 40 42"
        :class="className"
        v-bind="$attrs"
    >
        <path
            fill="currentColor"
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M12 4H28A7 7 0 0 1 35 11V31A7 7 0 0 1 28 38H12A7 7 0 0 1 5 31V11A7 7 0 0 1 12 4Z
               M12.5 21.5L18 27L29.5 14.5L27 12L18 21.5L15 19Z"
        />
    </svg>
</template>
