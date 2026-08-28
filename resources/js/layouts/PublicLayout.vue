<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Squiggle from '@/components/home/squiggle.vue'

const page = usePage()
// Same convention as the app header layout — adjust if your shared auth
// prop lives somewhere else (e.g. Breeze/Fortify default is page.props.auth.user).
const user = computed(() => page.props?.auth?.user ?? null)
const isLoggedIn = computed(() => !!user.value)
const initial = computed(() => (user.value?.name?.[0] ?? '?').toUpperCase())

const menuOpen = ref(false)
const menuRef = ref(null)

function toggleMenu() {
    menuOpen.value = !menuOpen.value
}
function closeMenu() {
    menuOpen.value = false
}
function onClickOutside(e) {
    if (menuOpen.value && menuRef.value && !menuRef.value.contains(e.target)) {
        closeMenu()
    }
}
function onKeydown(e) {
    if (e.key === 'Escape') closeMenu()
}

onMounted(() => {
    document.addEventListener('click', onClickOutside)
    document.addEventListener('keydown', onKeydown)
})
onUnmounted(() => {
    document.removeEventListener('click', onClickOutside)
    document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
    <div class="relative min-h-svh bg-background bg-dot-grid">
        <!-- paper grain, same texture used across the whole app -->
        <div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 opacity-[0.05] mix-blend-multiply">
            <svg width="100%" height="100%">
                <filter id="marketing-grain">
                    <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="2" stitchTiles="stitch" />
                    <feColorMatrix type="saturate" values="0" />
                </filter>
                <rect width="100%" height="100%" filter="url(#marketing-grain)" />
            </svg>
        </div>

        <header class="sticky top-0 z-30 border-b border-border bg-background/90 backdrop-blur-sm">
            <nav class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
                <Link :href="isLoggedIn ? '/asks' : '/'" class="group relative font-display font-bold text-2xl text-foreground">
                    Coa<span class="text-primary">xed</span>
                    <Squiggle
                        color="primary"
                        class-name="absolute -bottom-1.5 left-0 w-full h-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                    />
                </Link>

                <!-- logged out: marketing CTAs -->
                <div v-if="!isLoggedIn" class="flex items-center gap-3">
                    <Link
                        href="/login"
                        class="hidden sm:inline-block text-sm font-semibold text-foreground/70 hover:text-foreground transition"
                    >
                        Log in
                    </Link>
                    <Link
                        href="/register"
                        class="rounded-full bg-primary text-primary-foreground text-sm font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
                    >
                        Create yours
                    </Link>
                </div>

                <!-- logged in: dashboard link + account menu -->
                <div v-else class="flex items-center gap-3">
                    <Link
                        href="/asks"
                        class="hidden sm:inline-block rounded-full bg-primary text-primary-foreground text-sm font-semibold px-5 py-2.5 shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
                    >
                        Dashboard
                    </Link>

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
                                    href="/asks"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-foreground/80 hover:bg-accent/40 transition-colors sm:hidden"
                                    @click="closeMenu"
                                >
                                    Dashboard
                                </Link>

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
                </div>
            </nav>
        </header>

        <main class="relative z-10 overflow-x-hidden">
            <slot />
        </main>
    </div>
</template>
