<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden bg-background p-6 md:p-10"
    >
        <!-- corkboard texture: faint dot grid, matches the paper/pin motif on the login & register cards -->
        <div
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 opacity-[0.06]"
            style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 22px 22px"
        ></div>

        <div class="relative w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="home()" class="relative flex flex-col items-center gap-3 font-medium">
                        <!-- pinned logo card, same pin + edge-torn + shadow-paper treatment as the auth cards -->
                        <div class="relative">
                            <svg
                                aria-hidden="true"
                                class="pointer-events-none absolute -top-2.5 left-1/2 z-10 -translate-x-1/2 animate-bob"
                                width="20"
                                height="20"
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
                            <div
                                class="edge-torn flex h-16 w-16 -rotate-2 items-center justify-center rounded-lg border border-border bg-card shadow-paper transition-transform duration-200 hover:rotate-0"
                            >
                                <AppLogoIcon class="size-9 fill-current text-[var(--foreground)] dark:text-white" />
                            </div>
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>

                    <div class="space-y-1.5 text-center">
                        <h1 class="font-hand text-2xl -rotate-1 text-tertiary">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>

                <slot />
            </div>
        </div>
    </div>
</template>
