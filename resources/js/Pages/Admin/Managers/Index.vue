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
    columnHelper.accessor('created_at', {
        header: 'Created At',
        cell: (info) => h('span', { class: 'text-slate-500 text-xs' }, info.getValue()),
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
                        class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 rounded-lg shadow-2xs transition-colors',
                    },
                    () => 'Edit'
                ),
                h(
                    'button',
                    {
                        type: 'button',
                        onClick: () => openDeleteModal(manager),
                        class: 'inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 rounded-lg transition-colors',
                    },
                    'Delete'
                ),
            ])
        },
    }),
]
</script>

<template>
    <Head title="Manage Managers — Grand Horizon" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Manage Managers
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        View, search, create, update, and manage hotel manager accounts.
                    </p>
                </div>
                <Link :href="route('admin.managers.create')">
                    <PrimaryButton>
                        + Add Manager
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
                :data="managers.data"
                :pagination="managers"
                :search="filters.search"
                search-placeholder="Search managers by name, email, national ID..."
            />
        </div>

        <!-- AJAX Delete Modal -->
        <ConfirmDeleteModal
            :show="deleteModalOpen"
            title="Delete Manager"
            message="Are you sure you want to delete this manager? This will permanently remove their access."
            :item-title="managerToDelete?.name"
            :delete-url="managerToDelete ? route('admin.managers.destroy', managerToDelete.id) : ''"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
