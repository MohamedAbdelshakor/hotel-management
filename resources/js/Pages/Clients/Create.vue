<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    mobile: '',
    country: '',
    gender: 'Male',
    national_id: '',
    avatar_image: null,
})

const avatarPreview = ref(null)

function handleAvatarChange(e) {
    const file = e.target.files[0]
    if (file) {
        form.avatar_image = file
        avatarPreview.value = URL.createObjectURL(file)
    }
}

function submit() {
    form.post(route('clients.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Create Client" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-white leading-tight">
                        Create Client
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Register a new client profile with contact and personal details.
                    </p>
                </div>
                <Link :href="route('clients.index')">
                    <SecondaryButton>
                        Back to List
                    </SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-2xl mx-auto">
                <div class="bg-[#0f172a] shadow-2xl rounded-2xl p-6 sm:p-8 border border-slate-800/80">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Avatar Preview & Upload -->
                        <div>
                            <InputLabel value="Avatar Image (Optional - JPG, JPEG, PNG)" />
                            <div class="mt-2 flex items-center space-x-5">
                                <div class="relative">
                                    <img
                                        :src="avatarPreview || 'https://ui-avatars.com/api/?name=Client&background=6366f1&color=fff'"
                                        alt="Avatar preview"
                                        class="h-20 w-20 rounded-full object-cover border-2 border-indigo-200 dark:border-indigo-900 shadow-sm"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="file"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        @change="handleAvatarChange"
                                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 cursor-pointer"
                                    />
                                    <p class="text-[11px] text-gray-400 mt-1">
                                        Max size 2MB. Default avatar provided if none uploaded.
                                    </p>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.avatar_image" />
                        </div>

                        <!-- Name -->
                        <div>
                            <InputLabel for="name" value="Full Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                placeholder="e.g. Alex Johnson"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div>
                            <InputLabel for="email" value="Email Address" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                                placeholder="client@example.com"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password -->
                        <div>
                            <InputLabel for="password" value="Password (min 6 characters)" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- Grid: Mobile & Country -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="mobile" value="Mobile Phone" />
                                <TextInput
                                    id="mobile"
                                    v-model="form.mobile"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="+1 555 123 4567"
                                />
                                <InputError class="mt-2" :message="form.errors.mobile" />
                            </div>

                            <div>
                                <InputLabel for="country" value="Country" />
                                <TextInput
                                    id="country"
                                    v-model="form.country"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="e.g. Egypt, USA, UK"
                                />
                                <InputError class="mt-2" :message="form.errors.country" />
                            </div>
                        </div>

                        <!-- Grid: Gender & National ID -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="gender" value="Gender" />
                                <select
                                    id="gender"
                                    v-model="form.gender"
                                    class="mt-1 block w-full rounded-xl bg-[#131b2e] border border-slate-700/70 text-slate-100 shadow-inner focus:border-emerald-500/70 focus:ring-2 focus:ring-emerald-500/30 text-sm transition-all"
                                >
                                    <option value="Male" class="bg-[#0f172a] text-slate-100">Male</option>
                                    <option value="Female" class="bg-[#0f172a] text-slate-100">Female</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.gender" />
                            </div>

                            <div>
                                <InputLabel for="national_id" value="National ID (Optional)" />
                                <TextInput
                                    id="national_id"
                                    v-model="form.national_id"
                                    type="text"
                                    class="mt-1 block w-full font-mono"
                                    placeholder="e.g. 29810150102345"
                                />
                                <InputError class="mt-2" :message="form.errors.national_id" />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <Link :href="route('clients.index')">
                                <SecondaryButton>Cancel</SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save Client</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
