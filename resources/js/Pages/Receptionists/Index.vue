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
                    class: 'h-9 w-9 rounded-full object-cover border border-slate-200 shadow-2xs',
                })
            },
        }),
        columnHelper.accessor('name', {
            header: 'Name',
            cell: (info) => h('span', { class: 'font-semibold text-slate-900' }, info.getValue()),
        }),
        columnHelper.accessor('email', {
            header: 'Email',
            cell: (info) => h('span', { class: 'text-slate-600' }, info.getValue()),
        }),
        columnHelper.accessor('national_id', {
            header: 'National ID',
            cell: (info) => h('span', { class: 'font-mono text-xs bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-slate-700' }, info.getValue() || '—'),
        }),
    ]

    // "Manager Name" column shown only when logged in as Admin
    if (isAdmin.value) {
        cols.push(
            columnHelper.accessor('manager_name', {
                header: 'Created By Manager',
                cell: (info) => h('span', { class: 'font-medium text-slate-700' }, info.getValue() || 'Admin'),
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
                        ? 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200'
                        : 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200'
                }, isBanned ? 'Banned' : 'Active')
            },
        }),
        columnHelper.accessor('created_at', {
            header: 'Created At',
            cell: (info) => h('span', { class: 'text-slate-500 text-xs' }, info.getValue()),
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
                                    ? 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 rounded-lg transition-colors'
                                    : 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 rounded-lg transition-colors',
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
                                class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 rounded-lg shadow-2xs transition-colors',
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
                                class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 rounded-lg transition-colors',
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
    <Head title="Manage Receptionists — Grand Horizon" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Manage Receptionists
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        View, search, create, update, ban/unban, and manage hotel front desk staff.
                    </p>
                </div>
                <Link :href="route('receptionists.create')">
                    <PrimaryButton>
                        + Add Receptionist
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Flash messages -->
            <div
                v-if="$page.props.flash?.success"
                class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-2xs"
            >
                <span>{{ $page.props.flash.success }}</span>
            </div>
            <div
                v-if="$page.props.flash?.error"
                class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex items-center justify-between shadow-2xs"
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
