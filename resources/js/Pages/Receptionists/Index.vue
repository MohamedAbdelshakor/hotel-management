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
                    class: 'h-10 w-10 rounded-full object-cover border border-slate-700 shadow-sm',
                })
            },
        }),
        columnHelper.accessor('name', {
            header: 'Name',
            cell: (info) => h('span', { class: 'font-semibold text-white' }, info.getValue()),
        }),
        columnHelper.accessor('email', {
            header: 'Email',
            cell: (info) => h('span', { class: 'text-slate-400' }, info.getValue()),
        }),
        columnHelper.accessor('national_id', {
            header: 'National ID',
            cell: (info) => h('span', { class: 'font-mono text-xs bg-[#131b2e] border border-slate-700/80 px-2.5 py-1 rounded-md text-slate-300' }, info.getValue() || 'N/A'),
        }),
    ]

    // "Manager Name" column shown only when logged in as Admin
    if (isAdmin.value) {
        cols.push(
            columnHelper.accessor('manager_name', {
                header: 'Manager Name',
                cell: (info) => h('span', { class: 'font-semibold text-amber-400' }, info.getValue() || 'Admin'),
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
                        ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20'
                        : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                }, isBanned ? 'Banned' : 'Active')
            },
        }),
        columnHelper.accessor('created_at', {
            header: 'Created At',
            cell: (info) => h('span', { class: 'text-slate-400 text-xs' }, info.getValue()),
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
                                    ? 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500/20 rounded-lg transition-colors'
                                    : 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-amber-400 bg-amber-500/10 border border-amber-500/20 hover:bg-amber-500/20 rounded-lg transition-colors',
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
                                class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 hover:bg-cyan-500/20 rounded-lg transition-colors',
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
                                class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 rounded-lg transition-colors',
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
                    <h2 class="font-bold text-xl text-white leading-tight">
                        Manage Receptionists
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
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

        <div class="space-y-6">
            <!-- Flash messages -->
            <div
                v-if="$page.props.flash?.success"
                class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs font-medium flex items-center justify-between shadow-lg"
            >
                <span>{{ $page.props.flash.success }}</span>
            </div>
            <div
                v-if="$page.props.flash?.error"
                class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-300 text-xs font-medium flex items-center justify-between shadow-lg"
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
