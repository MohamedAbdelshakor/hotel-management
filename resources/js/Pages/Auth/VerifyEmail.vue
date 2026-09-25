<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Verify Email — Grand Horizon Hotel" />

        <div class="mb-6">
            <h2 class="text-xl font-bold tracking-tight text-slate-900">
                Verify your email address
            </h2>
            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                Thank you for registering! Please check your email inbox and click the verification link to activate your account.
            </p>
        </div>

        <div
            class="mb-5 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800"
            v-if="verificationLinkSent"
        >
            A fresh verification link has been sent to your registered email address.
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <PrimaryButton
                    class="w-full justify-center !py-3 !text-sm font-semibold tracking-normal"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Sending email...</span>
                    <span v-else>Resend Verification Email</span>
                </PrimaryButton>
            </div>

            <div class="text-center pt-2">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-xs font-semibold text-slate-600 hover:text-slate-900"
                >
                    Sign Out
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
