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
    columnHelper.accessor('created_at', {
        header: 'Created At',
        cell: (info) => h('span', { class: 'text-slate-400 text-xs' }, info.getValue()),
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
                        class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 hover:bg-emerald-500/20 rounded-lg transition-colors',
                    },
                    () => 'Edit'
                ),
                h(
                    'button',
                    {
                        type: 'button',
                        onClick: () => openDeleteModal(manager),
                        class: 'inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-400 bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 rounded-lg transition-colors',
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
                    <h2 class="font-bold text-xl text-white leading-tight">
                        Manage Managers
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
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
            message="Are you sure you want to delete this manager? This will permanently remove their account."
            :item-title="managerToDelete?.name"
            :delete-url="managerToDelete ? route('admin.managers.destroy', managerToDelete.id) : ''"
            @close="deleteModalOpen = false"
        />
    </AuthenticatedLayout>
</template>
