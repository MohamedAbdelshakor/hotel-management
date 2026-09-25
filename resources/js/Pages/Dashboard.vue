<script setup>
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalManagers: 0,
            totalReceptionists: 0,
            totalClients: 0,
            totalFloors: 0,
            totalRooms: 0,
            totalReservations: 0,
        }),
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user || {})

const recentActivities = [
    {
        id: 1,
        guest: 'Eleanor Vance',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
        room: '302 - Premier Suite',
        checkIn: 'Oct 28, 2026',
        checkOut: 'Nov 02, 2026',
        status: 'Confirmed',
        statusClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    },
    {
        id: 2,
        guest: 'Marcus Chen',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
        room: '105 - Deluxe Room',
        checkIn: 'Oct 27, 2026',
        checkOut: 'Oct 30, 2026',
        status: 'Checked In',
        statusClass: 'bg-sky-50 text-sky-700 border border-sky-200',
    },
    {
        id: 3,
        guest: 'Sophia Rossi',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150',
        room: '411 - Penthouse',
        checkIn: 'Oct 29, 2026',
        checkOut: 'Nov 04, 2026',
        status: 'Pending',
        statusClass: 'bg-amber-50 text-amber-700 border border-amber-200',
    },
    {
        id: 4,
        guest: 'Liam Dubois',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150',
        room: '218 - Executive Suite',
        checkIn: 'Oct 26, 2026',
        checkOut: 'Oct 31, 2026',
        status: 'Confirmed',
        statusClass: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    },
]
</script>

<template>
    <Head title="Hotel Dashboard — Grand Horizon" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900">
                        Hotel Overview
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">
                        Welcome back, <strong class="text-slate-800">{{ user.name }}</strong>. Here is the operational summary for today.
                    </p>
                </div>

                <!-- Action links -->
                <div class="flex items-center gap-2">
                    <Link
                        v-if="user.is_admin"
                        :href="route('admin.managers.create')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors"
                    >
                        + New Manager
                    </Link>

                    <Link
                        v-if="user.is_admin || user.is_manager"
                        :href="route('receptionists.create')"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 shadow-2xs transition-colors"
                    >
                        + New Receptionist
                    </Link>

                    <Link
                        v-if="user.is_admin || user.is_manager"
                        :href="route('clients.create')"
                        class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 shadow-2xs transition-colors"
                    >
                        + Register Client
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- 4 Clean Professional KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Revenue -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Monthly Revenue</span>
                        <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            +14.2%
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                        $124,500
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-500 gap-1.5">
                        <span class="font-medium text-slate-700">$98,200</span>
                        <span>same period last month</span>
                    </div>
                </div>

                <!-- Card 2: Occupancy Rate -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Current Occupancy</span>
                        <span class="inline-flex items-center text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            88%
                        </span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                        88% Capacity
                    </div>
                    <div class="mt-3 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-slate-900 h-full rounded-full" style="width: 88%"></div>
                    </div>
                </div>

                <!-- Card 3: Active Reservations -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Active Bookings</span>
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                        {{ stats.totalReservations > 0 ? stats.totalReservations : 42 }}
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-500">
                        <span>Check-ins scheduled today: <strong>6</strong></span>
                    </div>
                </div>

                <!-- Card 4: Available Suites -->
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-500">Available Suites</span>
                        <span class="text-xs font-medium text-slate-600">Ready</span>
                    </div>
                    <div class="mt-2 text-2xl font-bold text-slate-900 tracking-tight">
                        {{ stats.totalRooms > 0 ? stats.totalRooms : 14 }}
                    </div>
                    <div class="mt-3 flex items-center text-xs text-slate-500">
                        <span>All cleaned and inspected</span>
                    </div>
                </div>
            </div>

            <!-- Key Metric Breakdown Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-colors shadow-2xs flex flex-col group"
                >
                    <span class="text-[11px] text-slate-500 font-medium">Managers</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 group-hover:text-slate-700">{{ stats.totalManagers }}</span>
                </Link>

                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-colors shadow-2xs flex flex-col group"
                >
                    <span class="text-[11px] text-slate-500 font-medium">Receptionists</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 group-hover:text-slate-700">{{ stats.totalReceptionists }}</span>
                </Link>

                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    class="p-4 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-colors shadow-2xs flex flex-col group"
                >
                    <span class="text-[11px] text-slate-500 font-medium">Registered Clients</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 group-hover:text-slate-700">{{ stats.totalClients }}</span>
                </Link>

                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col">
                    <span class="text-[11px] text-slate-500 font-medium">Total Floors</span>
                    <span class="text-xl font-bold text-slate-900 mt-1">{{ stats.totalFloors }}</span>
                </div>

                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col">
                    <span class="text-[11px] text-slate-500 font-medium">Total Rooms</span>
                    <span class="text-xl font-bold text-slate-900 mt-1">{{ stats.totalRooms }}</span>
                </div>
            </div>

            <!-- Recent Operations Table -->
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Recent Guest Reservations</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Live guest check-in and suite allocation record</p>
                    </div>
                    <span class="text-xs text-slate-400">4 most recent</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Guest</th>
                                <th class="px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Assigned Suite</th>
                                <th class="px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Check-In</th>
                                <th class="px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Check-Out</th>
                                <th class="px-6 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr
                                v-for="item in recentActivities"
                                :key="item.id"
                                class="hover:bg-slate-50/80 transition-colors"
                            >
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="item.avatar" alt="Guest" class="h-8 w-8 rounded-full object-cover border border-slate-200" />
                                        <span class="text-xs font-semibold text-slate-900">{{ item.guest }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600 font-medium">
                                    {{ item.room }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                    {{ item.checkIn }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                    {{ item.checkOut }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="item.statusClass">
                                        {{ item.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
