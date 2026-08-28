<script setup>
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import {Form, Head} from '@inertiajs/vue3';
/* @chisel-passkeys */
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import Spinner from "@/components/Spinner.vue";
import GoogleSignInButton from "@/components/GoogleSignInButton.vue";
/* @end-chisel-passkeys */

defineOptions({
    layout: {
        title: 'Log in to your account',
        description: 'Enter your email and password below to log in',
    },
});

defineProps({
    status: {
        type: String,
        default: null,
    },
    canResetPassword: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Head title="Log in"/>

    <div class="relative mx-auto w-full max-w-sm">
        <svg aria-hidden="true" class="pointer-events-none absolute -top-2.5 left-1/2 -translate-x-1/2 animate-bob"
             width="22" height="22" viewBox="0 0 22 22">
            <circle cx="11" cy="9" r="7" class="fill-primary"/>
            <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45"/>
            <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5"
                  stroke-linecap="round"/>
        </svg>
        <span
            aria-hidden="true"
            class="pointer-events-none absolute -top-2.5 right-3 h-5 w-16 rotate-6 bg-secondary/40 shadow-sm"
            style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
        ></span>

        <div class="edge-torn rounded-xl border border-border bg-card px-10 pt-11 pb-10 shadow-paper">
            <div class="mb-8 text-center">
                <h1 class="text-lg font-semibold tracking-tight text-foreground">Welcome back</h1>
                <p class="mt-1.5 font-hand text-lg text-tertiary -rotate-1">log in to see who said yes</p>
            </div>

            <div
                v-if="status"
                class="mb-6 rounded-lg border border-tertiary/25 bg-tertiary/10 px-4 py-3 text-center text-sm font-medium text-tertiary"
            >
                {{ status }}
            </div>

            <GoogleSignInButton/>
            <div class="my-5 flex items-center gap-3">
                <div class="h-px flex-1 bg-border"></div>
                <span class="text-xs text-foreground/45">or</span>
                <div class="h-px flex-1 bg-border"></div>
            </div>
            <Form
                action="/login"
                method="post"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-7"
            >
                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="email" class="text-sm font-medium text-foreground/90">Email address</Label>
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
                                'h-11 rounded-lg border-1.5 px-4 text-[15px] transition-shadow',
                                errors.email && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.email"/>
                    </div>

                    <div class="grid gap-2">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-sm font-medium text-foreground/90">Password</Label>
                            <TextLink
                                v-if="canResetPassword"
                                href="/forgot-password"
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
                                'h-11 rounded-lg border-1.5 px-4 text-[15px] transition-shadow',
                                errors.password && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.password"/>
                    </div>

                    <Button
                        type="submit"
                        class="mt-1 h-11 w-full rounded-full bg-primary text-[15px] font-medium text-primary-foreground shadow-paper transition-all duration-200 hover:-translate-y-0.5 hover:rotate-1"
                        :tabindex="4"
                        :disabled="processing"
                        data-test="login-button"
                    >
                        <Spinner v-if="processing" class="mr-2"/>
                        Log in
                    </Button>
                </div>

                <!-- @chisel-registration -->
                <div class="text-center text-sm text-muted-foreground">
                    Don't have an account?
                    <TextLink href="/register" :tabindex="5" class="font-semibold text-primary">Sign up</TextLink>
                </div>
                <!-- @end-chisel-registration -->
            </Form>
        </div>
    </div>
</template>

<style scoped>
@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    25% {
        transform: translateX(-4px);
    }
    75% {
        transform: translateX(4px);
    }
}

.shake {
    animation: shake 0.35s;
}
</style>
