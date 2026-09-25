<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
    receptionist: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    _method: 'put',
    name: props.receptionist.name,
    email: props.receptionist.email,
    password: '',
    national_id: props.receptionist.national_id || '',
    avatar_image: null,
})

const avatarPreview = ref(props.receptionist.avatar_url)

function handleAvatarChange(e) {
    const file = e.target.files[0]
    if (file) {
        form.avatar_image = file
        avatarPreview.value = URL.createObjectURL(file)
    }
}

function submit() {
    form.post(route('receptionists.update', props.receptionist.id), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Edit Receptionist" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Edit Receptionist: {{ receptionist.name }}
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Update receptionist profile details, credentials, or avatar.
                    </p>
                </div>
                <Link :href="route('receptionists.index')">
                    <SecondaryButton>
                        Back to List
                    </SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Avatar Preview & Upload -->
                        <div>
                            <InputLabel value="Avatar Image (Optional - JPG, JPEG, PNG)" />
                            <div class="mt-2 flex items-center space-x-5">
                                <div class="relative">
                                    <img
                                        :src="avatarPreview"
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
                                        Upload a new image to replace current avatar. Max 2MB.
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
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <!-- Password (Optional on update) -->
                        <div>
                            <InputLabel for="password" value="New Password (Leave blank to keep existing)" />
                            <TextInput
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="••••••••"
                            />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <!-- National ID -->
                        <div>
                            <InputLabel for="national_id" value="National ID" />
                            <TextInput
                                id="national_id"
                                v-model="form.national_id"
                                type="text"
                                class="mt-1 block w-full font-mono"
                                required
                            />
                            <InputError class="mt-2" :message="form.errors.national_id" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <Link :href="route('receptionists.index')">
                                <SecondaryButton>Cancel</SecondaryButton>
                            </Link>
                            <PrimaryButton :disabled="form.processing">
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Receptionist</span>
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
