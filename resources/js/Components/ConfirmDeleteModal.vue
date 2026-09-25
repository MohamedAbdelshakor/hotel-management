<script setup>
import { ref } from 'vue'
import axios from 'axios'
import Modal from '@/Components/Modal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Delete Confirmation',
    },
    message: {
        type: String,
        default: 'Are you sure you want to delete this item? This action cannot be undone.',
    },
    itemTitle: {
        type: String,
        default: '',
    },
    deleteUrl: {
        type: String,
        required: true,
    },
})

const emit = defineEmits(['close', 'deleted'])

const processing = ref(false)
const errorMessage = ref('')

async function confirmDelete() {
    processing.value = true
    errorMessage.value = ''

    try {
        const response = await axios.delete(props.deleteUrl, {
            headers: {
                Accept: 'application/json',
            },
        })

        processing.value = false
        emit('deleted')
        emit('close')

        // Refresh Inertia data table seamlessly
        router.reload({ preserveScroll: true })
    } catch (error) {
        processing.value = false
        errorMessage.value = error.response?.data?.message || 'Failed to delete item.'
    }
}

function handleClose() {
    if (!processing.value) {
        errorMessage.value = ''
        emit('close')
    }
}
</script>

<template>
    <Modal :show="show" max-width="md" @close="handleClose">
        <div class="p-6">
            <div class="flex items-center space-x-3 text-red-400 mb-4">
                <div class="p-2.5 bg-red-500/10 border border-red-500/20 rounded-xl">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-white">
                    {{ title }}
                </h3>
            </div>

            <p class="text-xs text-slate-300 mb-2">
                {{ message }}
            </p>

            <p v-if="itemTitle" class="text-xs font-semibold text-slate-100 bg-[#131b2e] border border-slate-700/80 p-3 rounded-xl mb-4">
                "{{ itemTitle }}"
            </p>

            <div v-if="errorMessage" class="mb-4 p-3 bg-red-500/10 text-red-300 text-xs rounded-xl border border-red-500/30">
                {{ errorMessage }}
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <SecondaryButton :disabled="processing" @click="handleClose">
                    Cancel
                </SecondaryButton>
                <DangerButton :disabled="processing" @click="confirmDelete">
                    <span v-if="processing">Deleting...</span>
                    <span v-else>Delete</span>
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
