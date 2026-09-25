<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900 leading-tight">
                    Account Profile
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Manage your personal account credentials, email preferences, and security settings.
                </p>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-4xl mx-auto space-y-6">
                <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-2xs">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-2xs">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-2xs">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
