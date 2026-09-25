<script setup>
import { computed } from 'vue'
import {
    useVueTable,
    getCoreRowModel,
    FlexRender,
} from '@tanstack/vue-table'
import { router } from '@inertiajs/vue3'

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
        default: 'Search records...',
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
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    :value="search"
                    :placeholder="searchPlaceholder"
                    @input="handleSearch"
                    class="w-full pl-9 pr-3.5 py-2 text-xs rounded-lg border border-slate-300 bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 shadow-2xs transition-colors"
                />
            </div>
            <div class="flex items-center gap-2">
                <slot name="actions" />
            </div>
        </div>

        <!-- Table Card Container -->
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50/80">
                        <tr v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                            <th
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                scope="col"
                                class="px-6 py-3.5 text-[11px] font-semibold uppercase tracking-wider text-slate-500"
                            >
                                <FlexRender
                                    v-if="!header.isPlaceholder"
                                    :render="header.column.columnDef.header"
                                    :props="header.getContext()"
                                />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <tr
                            v-for="row in table.getRowModel().rows"
                            :key="row.id"
                            class="hover:bg-slate-50/80 transition-colors"
                        >
                            <td
                                v-for="cell in row.getVisibleCells()"
                                :key="cell.id"
                                class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-700"
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
                                class="px-6 py-12 text-center text-sm text-slate-500"
                            >
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <span class="font-medium text-slate-700">No records found</span>
                                    <p class="text-xs text-slate-400">Try modifying your search query</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Server-Side Pagination Controls -->
            <div
                v-if="pagination && pagination.total > 0"
                class="px-6 py-3.5 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/60"
            >
                <div class="text-xs text-slate-500">
                    Showing <span class="font-medium text-slate-800">{{ pagination.from || 0 }}</span> to
                    <span class="font-medium text-slate-800">{{ pagination.to || 0 }}</span> of
                    <span class="font-medium text-slate-800">{{ pagination.total }}</span> results
                </div>
                <div class="flex items-center space-x-1">
                    <button
                        v-for="(link, index) in pagination.links"
                        :key="index"
                        :disabled="!link.url || link.active"
                        @click="goToPage(link.url)"
                        v-html="link.label"
                        class="px-2.5 py-1 text-xs rounded-lg transition-colors"
                        :class="[
                            link.active
                                ? 'bg-slate-900 text-white font-medium shadow-2xs'
                                : link.url
                                ? 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300'
                                : 'bg-transparent text-slate-400 cursor-not-allowed border border-transparent'
                        ]"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
