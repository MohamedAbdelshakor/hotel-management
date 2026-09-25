<script setup>
import { computed } from 'vue'
import {
    useVueTable,
    getCoreRowModel,
    FlexRender,
} from '@tanstack/vue-table'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: true,
    },
    pagination: {
        type: Object,
        required: true,
    },
    search: {
        type: String,
        default: '',
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...',
    },
})

const emit = defineEmits(['update:search', 'search'])

const table = useVueTable({
    get data() {
        return props.data
    },
    columns: props.columns,
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount: props.pagination.last_page ?? 1,
})

function handleSearch(e) {
    const value = e.target.value
    emit('update:search', value)
    router.get(
        window.location.pathname,
        { search: value },
        { preserveState: true, preserveScroll: true, replace: true }
    )
}

function goToPage(url) {
    if (!url) return
    router.get(url, {}, { preserveState: true, preserveScroll: true })
}
</script>

<template>
    <div class="space-y-4">
        <!-- Search and Action Header -->
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
            <div class="relative max-w-sm w-full">
                <input
                    type="text"
                    :value="search"
                    :placeholder="searchPlaceholder"
                    @input="handleSearch"
                    class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <th
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                scope="col"
                                class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider"
                            >
                                <FlexRender
                                    v-if="!header.isPlaceholder"
                                    :render="header.column.columnDef.header"
                                    :props="header.getContext()"
                                />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        <tr
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            class="hover:bg-gray-50/75 dark:hover:bg-gray-700/50 transition-colors"
                        >
                            <td
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200"
                            >
                                <FlexRender
                                    :render="cell.column.columnDef.cell"
                                    :props="cell.getContext()"
                                />
                            </td>
                        </tr>
                        <tr v-if="table.getRowModel().rows.length === 0">
                            <td
                                :colspan="columns.length"
                                class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400"
                            >
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <span class="font-medium">No records found</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Server-Side Pagination Controls -->
            <div
                v-if="pagination && pagination.total > 0"
                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-gray-900/30"
            >
                <div class="text-xs text-gray-600 dark:text-gray-400">
                    Showing <span class="font-semibold text-gray-900 dark:text-gray-100">{{ pagination.from || 0 }}</span> to
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ pagination.to || 0 }}</span> of
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ pagination.total }}</span> results
                </div>
                <div class="flex items-center space-x-1">
                    <button
                        v-for="(link, index) in pagination.links"
                        :key="index"
                        :disabled="!link.url || link.active"
                        @click="goToPage(link.url)"
                        v-html="link.label"
                        class="px-3 py-1.5 text-xs rounded-md transition-colors"
                        :class="[
                            link.active
                                ? 'bg-indigo-600 text-white font-semibold shadow-sm'
                                : link.url
                                ? 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600'
                                : 'bg-transparent text-gray-400 dark:text-gray-600 cursor-not-allowed border border-transparent'
                        ]"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
