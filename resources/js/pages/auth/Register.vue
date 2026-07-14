<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>
    <Head title="Register" />

    <div class="relative mx-auto w-full max-w-sm">
        <svg aria-hidden="true" class="pointer-events-none absolute -top-2.5 left-1/2 -translate-x-1/2 animate-bob" width="22" height="22" viewBox="0 0 22 22">
            <circle cx="11" cy="9" r="7" class="fill-tertiary" />
            <circle cx="8.5" cy="6.5" r="2" fill="white" fill-opacity="0.45" />
            <line x1="11" y1="15" x2="11" y2="21" stroke="#2B2320" stroke-opacity="0.25" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <span
            aria-hidden="true"
            class="pointer-events-none absolute -top-2.5 right-3 h-5 w-16 rotate-6 bg-primary/30 shadow-sm"
            style="clip-path: polygon(4% 0%, 100% 0%, 96% 100%, 0% 100%)"
        ></span>

        <div class="edge-torn rounded-lg border border-border bg-card px-8 pt-9 pb-8 shadow-paper">
            <p class="mb-6 text-center font-hand text-xl text-tertiary -rotate-1">costs nothing, takes a minute</p>

            <Form
                v-bind="store.form()"
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-6"
            >
                <div class="grid gap-5">
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="Full name"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.name && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="email@example.com"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.email && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.email" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="username">Username</Label>
                        <Input
                            id="username"
                            type="username"
                            required
                            :tabindex="2"
                            autocomplete="username"
                            name="username"
                            placeholder="joe 123"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.username && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.username" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">Password</Label>
                        <PasswordInput
                            id="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="Password"
                            :passwordrules="passwordRules"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.password && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <PasswordInput
                            id="password_confirmation"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="Confirm password"
                            :passwordrules="passwordRules"
                            :class="[
                                'rounded-lg border-1.5 transition-shadow',
                                errors.password_confirmation && 'border-destructive shake ring-2 ring-destructive/15',
                            ]"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <Button
                        type="submit"
                        class="mt-1 w-full rounded-full bg-primary text-primary-foreground shadow-paper hover:-translate-y-0.5 hover:rotate-1 transition-all duration-200"
                        tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button"
                    >
                        <Spinner v-if="processing" />
                        Create account
                    </Button>
                </div>

                <div class="text-center text-sm text-muted-foreground">
                    Already have an account?
                    <TextLink
                        :href="login()"
                        class="font-semibold text-primary underline underline-offset-4"
                        :tabindex="6"
                    >Log in</TextLink
                    >
                </div>
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
