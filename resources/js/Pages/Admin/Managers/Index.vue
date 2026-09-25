<script setup>
import { ref, h } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { createColumnHelper } from '@tanstack/vue-table'

const props = defineProps({
    managers: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
})

const columnHelper = createColumnHelper()

// Delete modal state
const deleteModalOpen = ref(false)
const managerToDelete = ref(null)

function openDeleteModal(manager) {
    managerToDelete.value = manager
    deleteModalOpen.value = true
}

const columns = [
    columnHelper.accessor('avatar_url', {
        header: 'Avatar',
        cell: (info) => {
            const url = info.getValue()
            return h('img', {
                src: url,
                alt: 'Avatar',
                class: 'h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-700 shadow-sm',
            })
        },
    }),
    columnHelper.accessor('name', {
        header: 'Name',
        cell: (info) => h('span', { class: 'font-medium text-gray-900 dark:text-gray-100' }, info.getValue()),
    }),
    columnHelper.accessor('email', {
        header: 'Email',
        cell: (info) => h('span', { class: 'text-gray-600 dark:text-gray-300' }, info.getValue()),
    }),
    columnHelper.accessor('national_id', {
        header: 'National ID',
        cell: (info) => h('span', { class: 'font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-gray-800 dark:text-gray-200' }, info.getValue() || 'N/A'),
    }),
    columnHelper.accessor('created_at', {
        header: 'Created At',
        cell: (info) => h('span', { class: 'text-gray-500 dark:text-gray-400 text-xs' }, info.getValue()),
    }),
    columnHelper.display({
        id: 'actions',
        header: 'Actions',
        cell: (info) => {
            const manager = info.row.original
            return h('div', { class: 'flex items-center space-x-2' }, [
                h(
                    Link,
                    {
                        href: route('admin.managers.edit', manager.id),
                        class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-md transition-colors',
                    },
                    () => 'Edit'
                ),
                h(
                    'button',
                    {
                        type: 'button',
                        onClick: () => openDeleteModal(manager),
                        class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-md transition-colors',
                    },
                    'Delete'
                ),
            ])
        },
    }),
]
</script>

<template>
    <Head title="Manage Managers" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Manage Managers
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        View, search, create, update and delete hotel managers.
                    </p>
                </div>
                <Link :href="route('admin.managers.create')">
                    <PrimaryButton>
                        + Create Manager
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Flash messages -->
                <div
                    v-if="$page.props.flash?.success"
                    class="p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 text-sm font-medium flex items-center justify-between"
                >
                    <span>{{ $page.props.flash.success }}</span>
                </div>
                <div
                    v-if="$page.props.flash?.error"
                    class="p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 text-sm font-medium flex items-center justify-between"
                >
                    <span>{{ $page.props.flash.error }}</span>
                </div>

                <!-- TanStack Data Table -->
                <DataTable
                    :columns="columns"
                    :data="managers.data"
                    :pagination="managers"
                    :search="filters.search"
                    search-placeholder="Search managers by name, email, national ID..."
                />
            </div>
        </div>

        <!-- AJAX Delete Modal -->
        <ConfirmDeleteModal
            :show="deleteModalOpen"
            title="Delete Manager"
            message="Are you sure you want to delete this manager? This will permanently remove their account."
            :item-title="managerToDelete?.name"
            :delete-url="managerToDelete ? route('admin.managers.destroy', managerToDelete.id) : ''"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
