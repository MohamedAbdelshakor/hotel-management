<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password — Grand Horizon Hotel" />

        <div class="mb-6">
            <h2 class="text-xl font-bold tracking-tight text-slate-900">
                Confirm your password
            </h2>
            <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                This is a secure area of the hotel portal. Please re-enter your password to proceed.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full justify-center !py-3 !text-sm font-semibold tracking-normal"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Verifying...</span>
                    <span v-else>Confirm</span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
