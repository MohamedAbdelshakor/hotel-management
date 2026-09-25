<script setup>
import { ref, h } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { createColumnHelper } from '@tanstack/vue-table'

const props = defineProps({
    clients: {
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
const clientToDelete = ref(null)

function openDeleteModal(client) {
    clientToDelete.value = client
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
    columnHelper.accessor('mobile', {
        header: 'Mobile',
        cell: (info) => h('span', { class: 'text-slate-600 text-xs font-mono' }, info.getValue() || '—'),
    }),
    columnHelper.accessor('country', {
        header: 'Country',
        cell: (info) => h('span', { class: 'text-slate-700 font-medium' }, info.getValue() || '—'),
    }),
    columnHelper.accessor('gender', {
        header: 'Gender',
        cell: (info) => h('span', { class: 'text-slate-500 text-xs' }, info.getValue() || '—'),
    }),
    columnHelper.accessor('is_approved', {
        header: 'Status',
        cell: (info) => {
            const approved = info.getValue()
            return h('span', {
                class: approved
                    ? 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200'
                    : 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200'
            }, approved ? 'Approved' : 'Pending')
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
            const client = info.row.original
            const buttons = []

            if (client.can?.update) {
                buttons.push(
                    h(
                        Link,
                        {
                            href: route('clients.edit', client.id),
                            class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 rounded-lg shadow-2xs transition-colors',
                        },
                        () => 'Edit'
                    )
                )
            }

            if (client.can?.delete) {
                buttons.push(
                    h(
                        'button',
                        {
                            type: 'button',
                            onClick: () => openDeleteModal(client),
                            class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 rounded-lg transition-colors',
                        },
                        'Delete'
                    )
                )
            }

            return h('div', { class: 'flex items-center space-x-2' }, buttons)
        },
    }),
]
</script>

<template>
    <Head title="Manage Clients — Grand Horizon" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Manage Clients
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        View, search, create, update, and manage hotel guest accounts.
                    </p>
                </div>
                <Link :href="route('clients.create')">
                    <PrimaryButton>
                        + Register Client
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
                :data="clients.data"
                :pagination="clients"
                :search="filters.search"
                search-placeholder="Search clients by name, email, phone, country..."
            />
        </div>

        <!-- AJAX Delete Modal -->
        <ConfirmDeleteModal
            :show="deleteModalOpen"
            title="Delete Client"
            message="Are you sure you want to delete this client? This action will permanently remove their profile."
            :item-title="clientToDelete?.name"
            :delete-url="clientToDelete ? route('clients.destroy', clientToDelete.id) : ''"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
