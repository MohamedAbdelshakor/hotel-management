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
    <Head title="Edit Receptionist — Grand Horizon" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-slate-900 leading-tight">
                        Edit Receptionist: {{ receptionist.name }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
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

        <div class="py-4">
            <div class="max-w-2xl mx-auto">
                <div class="bg-white shadow-2xs rounded-xl p-6 sm:p-8 border border-slate-200">
                    <form @submit.prevent="submit" class="space-y-5">
                        <!-- Avatar Preview & Upload -->
                        <div>
                            <InputLabel value="Avatar Image (Optional - JPG, JPEG, PNG)" />
                            <div class="mt-2 flex items-center space-x-5">
                                <div class="relative">
                                    <img
                                        :src="avatarPreview"
                                        alt="Avatar preview"
                                        class="h-16 w-16 rounded-full object-cover border-2 border-slate-200 shadow-2xs"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="file"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        @change="handleAvatarChange"
                                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer"
                                    />
                                    <p class="text-[11px] text-slate-400 mt-1">
                                        Upload a new image to replace current avatar. Max 2MB.
                                    </p>
                                </div>
                            </div>
                            <InputError class="mt-1.5" :message="form.errors.avatar_image" />
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
                            <InputError class="mt-1.5" :message="form.errors.name" />
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
                            <InputError class="mt-1.5" :message="form.errors.email" />
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
                            <InputError class="mt-1.5" :message="form.errors.password" />
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
                            <InputError class="mt-1.5" :message="form.errors.national_id" />
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
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
