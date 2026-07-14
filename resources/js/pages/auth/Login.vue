<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
/* @chisel-registration */
import { register } from '@/routes';
/* @end-chisel-registration */
import { store } from '@/routes/login';
import { request } from '@/routes/password';
/* @chisel-passkeys */
import PasskeyVerify from '@/components/PasskeyVerify.vue';
/* @end-chisel-passkeys */

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div class="relative mx-auto w-full max-w-sm">
        <!-- pin + tape: same corkboard treatment as the landing page cards -->
        <svg aria-hidden="true" class="pointer-events-none absolute -top-2.5 left-1/2 -translate-x-1/2 animate-bob" width="22" height="22" viewBox="0 0 22 22">
            <circle cx="11" cy="9" r="7" class="fill-primary" />
            <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
            <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <span
            aria-hidden="true"
            class="pointer-events-none absolute -top-2.5 right-3 h-5 w-16 rotate-6 bg-secondary/40 shadow-sm"
            style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
        ></span>

        <div class="edge-torn rounded-lg border border-border bg-card px-8 pt-9 pb-8 shadow-paper">
            <p class="mb-6 text-center font-hand text-xl text-tertiary -rotate-1">log in to see who said yes</p>

            <div v-if="status" class="mb-5 rounded-lg border border-tertiary/25 bg-tertiary/10 px-4 py-2.5 text-center text-sm font-semibold text-tertiary">
                {{ status }}
            </div>

            <!-- @chisel-passkeys -->
            <PasskeyVerify />
            <!-- @end-chisel-passkeys -->

            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="email@example.com"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.email && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password">Password</Label>
                            <TextLink
                                v-if="canResetPassword"
                                :href="request()"
                                class="font-hand text-base text-tertiary hover:text-primary"
                                :tabindex="5"
                            >
                                forgot it?
                            </TextLink>
                        </div>
                        <PasswordInput
                            id="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="Password"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.password && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="flex items-center justify-between">
                        <Label for="remember" class="flex items-center space-x-3">
                            <Checkbox id="remember" name="remember" :tabindex="3" />
                            <span class="text-sm text-foreground/75">Remember me</span>
                        </Label>
                    </div>

                    <Button
                        type="submit"
                        class="mt-1 w-full rounded-full bg-primary text-primary-foreground shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" />
                        Log in
                    </Button>
                </div>

                <!-- @chisel-registration -->
                <div class="text-center text-sm text-muted-foreground">
                    Don't have an account?
                    <TextLink :href="register()" :tabindex="5" class="font-semibold text-primary">Sign up</TextLink>
                </div>
                <!-- @end-chisel-registration -->
            </Form>
        </div>
    </div>
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
