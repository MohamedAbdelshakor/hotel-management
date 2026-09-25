<script setup>
import { ref, computed, h } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { createColumnHelper } from '@tanstack/vue-table'

const props = defineProps({
    receptionists: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
})

const page = usePage()
const isAdmin = computed(() => Boolean(page.props.auth?.user?.is_admin))

const columnHelper = createColumnHelper()

// Delete modal state
const deleteModalOpen = ref(false)
const receptionistToDelete = ref(null)

function openDeleteModal(receptionist) {
    receptionistToDelete.value = receptionist
    deleteModalOpen.value = true
}

function toggleBan(receptionist) {
    const action = receptionist.is_banned ? 'unban' : 'ban'
    const routeName = receptionist.is_banned ? 'receptionists.unban' : 'receptionists.ban'

    router.post(route(routeName, receptionist.id), {}, {
        preserveScroll: true,
    })
}

const columns = computed(() => {
    const cols = [
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
    ]

    // "Manager Name" column shown only when logged in as Admin
    if (isAdmin.value) {
        cols.push(
            columnHelper.accessor('manager_name', {
                header: 'Manager Name',
                cell: (info) => h('span', { class: 'font-medium text-indigo-600 dark:text-indigo-400' }, info.getValue() || 'Admin'),
            })
        )
    }

    cols.push(
        columnHelper.accessor('is_banned', {
            header: 'Status',
            cell: (info) => {
                const isBanned = info.getValue()
                return h('span', {
                    class: isBanned
                        ? 'inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300'
                        : 'inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                }, isBanned ? 'Banned' : 'Active')
            },
        }),
        columnHelper.accessor('created_at', {
            header: 'Created At',
            cell: (info) => h('span', { class: 'text-gray-500 dark:text-gray-400 text-xs' }, info.getValue()),
        }),
        columnHelper.display({
            id: 'actions',
            header: 'Actions',
            cell: (info) => {
                const receptionist = info.row.original
                const buttons = []

                // Ban / Unban button
                if (receptionist.can?.ban) {
                    buttons.push(
                        h(
                            'button',
                            {
                                type: 'button',
                                onClick: () => toggleBan(receptionist),
                                class: receptionist.is_banned
                                    ? 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 rounded-md transition-colors'
                                    : 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 rounded-md transition-colors',
                            },
                            receptionist.is_banned ? 'Unban' : 'Ban'
                        )
                    )
                }

                // Edit button
                if (receptionist.can?.update) {
                    buttons.push(
                        h(
                            Link,
                            {
                                href: route('receptionists.edit', receptionist.id),
                                class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 rounded-md transition-colors',
                            },
                            () => 'Edit'
                        )
                    )
                }

                // Delete button
                if (receptionist.can?.delete) {
                    buttons.push(
                        h(
                            'button',
                            {
                                type: 'button',
                                onClick: () => openDeleteModal(receptionist),
                                class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 dark:text-red-300 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-md transition-colors',
                            },
                            'Delete'
                        )
                    )
                }

                return h('div', { class: 'flex items-center space-x-2' }, buttons)
            },
        })
    )

    return cols
})
</script>

<template>
    <Head title="Manage Receptionists" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Manage Receptionists
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        View, search, create, update, ban/unban and delete hotel receptionists.
                    </p>
                </div>
                <Link :href="route('receptionists.create')">
                    <PrimaryButton>
                        + Create Receptionist
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
                    :data="receptionists.data"
                    :pagination="receptionists"
                    :search="filters.search"
                    search-placeholder="Search receptionists by name, email, national ID..."
                />
            </div>
        </div>

        <!-- AJAX Delete Modal -->
        <ConfirmDeleteModal
            :show="deleteModalOpen"
            title="Delete Receptionist"
            message="Are you sure you want to delete this receptionist? This action cannot be undone."
            :item-title="receptionistToDelete?.name"
            :delete-url="receptionistToDelete ? route('receptionists.destroy', receptionistToDelete.id) : ''"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
