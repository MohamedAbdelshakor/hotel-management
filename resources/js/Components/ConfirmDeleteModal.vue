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
            <div class="flex items-center space-x-3 text-red-600 mb-4">
                <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ title }}
                </h3>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                {{ message }}
            </p>

            <p v-if="itemTitle" class="text-sm font-medium text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-700/50 p-2.5 rounded-lg mb-4">
                "{{ itemTitle }}"
            </p>

            <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs rounded-lg border border-red-200 dark:border-red-800">
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
