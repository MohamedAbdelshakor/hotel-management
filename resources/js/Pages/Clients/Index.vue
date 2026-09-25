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
    columnHelper.accessor('mobile', {
        header: 'Mobile',
        cell: (info) => h('span', { class: 'text-slate-400 text-xs font-mono' }, info.getValue() || 'N/A'),
    }),
    columnHelper.accessor('country', {
        header: 'Country',
        cell: (info) => h('span', { class: 'text-slate-300 font-medium' }, info.getValue() || 'N/A'),
    }),
    columnHelper.accessor('gender', {
        header: 'Gender',
        cell: (info) => h('span', { class: 'text-slate-400 text-xs' }, info.getValue() || 'N/A'),
    }),
    columnHelper.accessor('is_approved', {
        header: 'Status',
        cell: (info) => {
            const approved = info.getValue()
            return h('span', {
                class: approved
                    ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'
                    : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20'
            }, approved ? 'Approved' : 'Pending')
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
            const client = info.row.original
            const buttons = []

            if (client.can?.update) {
                buttons.push(
                    h(
                        Link,
                        {
                            href: route('clients.edit', client.id),
                            class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-cyan-400 bg-cyan-500/10 border border-cyan-500/20 hover:bg-cyan-500/20 rounded-lg transition-colors',
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
                            class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 rounded-lg transition-colors',
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
    <Head title="Manage Clients" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-xl text-white leading-tight">
                        Manage Clients
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        View, search, create, update and delete hotel client accounts.
                    </p>
                </div>
                <Link :href="route('clients.create')">
                    <PrimaryButton>
                        + Create Client
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
