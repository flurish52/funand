<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import AppLogoIcon from '../components/AppLogo.vue'

const page = usePage();
// Adjust this to wherever your auth user actually lives in shared props
// (Breeze/Fortify starter kits typically expose it at page.props.auth.user).
const user = computed(() => (page.props as any)?.auth?.user ?? null);
const initial = computed(() => (user.value?.name?.[0] ?? '?').toUpperCase());

const menuOpen = ref(false);
const menuRef = ref<HTMLElement | null>(null);

function toggleMenu() {
    menuOpen.value = !menuOpen.value;
}
function closeMenu() {
    menuOpen.value = false;
}
function onClickOutside(e: MouseEvent) {
    if (menuOpen.value && menuRef.value && !menuRef.value.contains(e.target as Node)) {
        closeMenu();
    }
}
function onKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') closeMenu();
}

onMounted(() => {
    document.addEventListener('click', onClickOutside);
    document.addEventListener('keydown', onKeydown);
});
onUnmounted(() => {
    document.removeEventListener('click', onClickOutside);
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <div class="relative min-h-svh bg-background bg-dot-grid">
        <!-- paper grain, same texture used across the whole app -->
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 opacity-[0.05] mix-blend-multiply">
            <svg width="100%" height="100%">
                <filter id="app-grain">
                    <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch" />
                    <feColorMatrix type="saturate" values="0" />
                </filter>
                <rect width="100%" height="100%" filter="url(#app-grain)" />
            </svg>
        </div>

        <header class="sticky top-0 z-40 border-b border-border bg-background/90 backdrop-blur-sm">
            <nav class="max-w-5xl mx-auto flex items-center justify-between gap-4 px-4 sm:px-6 py-3">
                <!-- logo — takes you back to your asks -->
                <Link href="/asks" class="flex items-center gap-2 shrink-0">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary shadow-paper -rotate-2">
                        <AppLogoIcon class="size-5 fill-current text-primary-foreground" />
                    </div>
                    <span class="font-display font-bold text-lg text-foreground hidden xs:inline">
                        Only<span class="text-primary">Yes</span>
                    </span>
                </Link>

                <!-- avatar / account menu -->
                <div ref="menuRef" class="relative">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-full border border-border bg-card pl-1 pr-1 sm:pr-3 py-1 shadow-sm hover:border-tertiary transition-colors"
                        :aria-expanded="menuOpen"
                        aria-haspopup="true"
                        @click="toggleMenu"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-tertiary text-tertiary-foreground font-display font-semibold text-sm shrink-0">
                            {{ initial }}
                        </span>
                        <span class="hidden sm:inline text-sm font-medium text-foreground/80 max-w-[9rem] truncate">
                            {{ user?.name ?? 'Account' }}
                        </span>
                        <svg
                            class="hidden sm:block shrink-0 text-foreground/40 transition-transform"
                            :class="{ 'rotate-180': menuOpen }"
                            width="14" height="14" viewBox="0 0 14 14" fill="none"
                        >
                            <path d="M3.5 5.25L7 8.75L10.5 5.25" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <Transition
                        enter-active-class="transition duration-150 ease-out"
                        enter-from-class="opacity-0 -translate-y-1 scale-95"
                        leave-active-class="transition duration-100 ease-in"
                        leave-to-class="opacity-0 -translate-y-1 scale-95"
                    >
                        <div
                            v-if="menuOpen"
                            class="absolute right-0 mt-2 w-48 rounded-lg border border-border bg-card shadow-paper py-1.5 origin-top-right"
                        >
                            <div class="px-3.5 py-2 border-b border-border/70 sm:hidden">
                                <p class="text-sm font-semibold text-foreground truncate">{{ user?.name ?? 'Account' }}</p>
                                <p v-if="user?.email" class="text-xs text-foreground/50 truncate">{{ user.email }}</p>
                            </div>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-2.5 px-3.5 py-2 text-sm text-destructive hover:bg-destructive/10 transition-colors"
                                @click="closeMenu"
                            >
                                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" class="shrink-0">
                                    <path d="M6 14H3.5a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1H6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" />
                                    <path d="M10.5 11l3-3-3-3M13.2 8H6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Log out
                            </Link>
                        </div>
                    </Transition>
                </div>
            </nav>
        </header>

        <main class="relative z-10 overflow-x-hidden">
            <slot />
        </main>
    </div>
</template>
