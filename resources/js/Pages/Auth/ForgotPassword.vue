<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password — Grand Horizon Hotel" />

        <div class="mb-6">
            <h2 class="text-xl font-bold tracking-tight text-slate-900">
                Reset your password
            </h2>
            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                Enter your registered email address and we'll send you instructions to reset your account password.
            </p>
        </div>

        <div
            v-if="status"
            class="mb-5 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="email" value="Email Address" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@hotel.com"
                />
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center !py-3 !text-sm font-semibold tracking-normal"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending link...</span>
                    <span v-else>Send Password Reset Link</span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-3 text-xs text-slate-500">
                Remembered your password?
                <Link :href="route('login')" class="font-semibold text-slate-900 hover:underline ms-1">
                    Return to login
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
