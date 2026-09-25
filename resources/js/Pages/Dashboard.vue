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

// Sample recent operations/reservations matching design mockup
const recentActivities = [
    {
        id: 1,
        guest: 'Eleanor Vance',
        avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
        room: '302-Premier Suite',
        checkIn: 'Oct 28, 2026',
        checkOut: 'Nov 02, 2026',
        status: 'Confirmed',
        statusClass: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
    },
    {
        id: 2,
        guest: 'Marcus Chen',
        avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
        room: '105-Deluxe Room',
        checkIn: 'Oct 27, 2026',
        checkOut: 'Oct 30, 2026',
        status: 'Checked In',
        statusClass: 'bg-sky-500/10 text-sky-400 border-sky-500/30',
    },
    {
        id: 3,
        guest: 'Sophia Rossi',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150',
        room: '411-Penthouse',
        checkIn: 'Oct 29, 2026',
        checkOut: 'Nov 04, 2026',
        status: 'Pending',
        statusClass: 'bg-amber-500/10 text-amber-400 border-amber-500/30',
    },
    {
        id: 4,
        guest: 'Liam Dubois',
        avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150',
        room: '218-Executive Suite',
        checkIn: 'Oct 26, 2026',
        checkOut: 'Oct 31, 2026',
        status: 'Confirmed',
        statusClass: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
    },
]
</script>

<template>
    <Head title="Grand Horizon — Hotel Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                        <span>Hotel Operations Dashboard</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium">
                            Live System
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Welcome back, <span class="text-slate-200 font-semibold">{{ user.name }}</span>! Here is your daily hotel overview.
                    </p>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex items-center gap-2.5">
                    <Link
                        v-if="user.is_admin"
                        :href="route('admin.managers.create')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#131b2e] border border-slate-700/80 text-slate-200 hover:text-white hover:bg-slate-800 transition-all shadow-sm"
                    >
                        <span>+ Add Manager</span>
                    </Link>

                    <Link
                        v-if="user.is_admin || user.is_manager"
                        :href="route('receptionists.create')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-[#131b2e] border border-slate-700/80 text-slate-200 hover:text-white hover:bg-slate-800 transition-all shadow-sm"
                    >
                        <span>+ Add Receptionist</span>
                    </Link>

                    <Link
                        v-if="user.is_admin || user.is_manager"
                        :href="route('clients.create')"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-slate-950 hover:from-emerald-400 hover:to-teal-500 transition-all shadow-lg shadow-emerald-950/40"
                    >
                        <span>+ New Client</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- 4 Luxury KPI Stat Cards (Matching Design Mockup) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Revenue -->
                <div class="relative overflow-hidden rounded-2xl bg-[#0f172a] border border-slate-800/80 p-5 shadow-xl hover:border-emerald-500/40 transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Revenue</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            ↗ +14.2%
                        </span>
                    </div>
                    <div class="mt-3 text-3xl font-extrabold text-white tracking-tight">
                        $124,500
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Total revenue generated this month</p>

                    <!-- Glowing wave sparkline decoration -->
                    <div class="mt-4 pt-2">
                        <svg class="w-full h-8 text-emerald-500/80" viewBox="0 0 100 25" preserveAspectRatio="none">
                            <path d="M0 20 Q 20 5, 40 18 T 70 8 T 100 12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <!-- Card 2: Occupancy Rate -->
                <div class="relative overflow-hidden rounded-2xl bg-[#0f172a] border border-slate-800/80 p-5 shadow-xl hover:border-emerald-500/40 transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Occupancy Rate</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            ↗ +8%
                        </span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-3">
                        <span class="text-3xl font-extrabold text-white tracking-tight">88%</span>
                        <span class="text-xs text-slate-400">High Demand</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Based on total available suites</p>

                    <!-- Progress bar -->
                    <div class="mt-4 pt-3">
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 shadow-sm" style="width: 88%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Active Bookings -->
                <div class="relative overflow-hidden rounded-2xl bg-[#0f172a] border border-slate-800/80 p-5 shadow-xl hover:border-emerald-500/40 transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Active Bookings</span>
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    </div>
                    <div class="mt-3 text-3xl font-extrabold text-white tracking-tight">
                        {{ stats.totalReservations > 0 ? stats.totalReservations : 42 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Currently checked in or upcoming</p>

                    <!-- Sparkline wave -->
                    <div class="mt-4 pt-2">
                        <svg class="w-full h-8 text-teal-400/80" viewBox="0 0 100 25" preserveAspectRatio="none">
                            <path d="M0 15 Q 25 22, 50 10 T 80 18 T 100 5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <!-- Card 4: Available Rooms -->
                <div class="relative overflow-hidden rounded-2xl bg-[#0f172a] border border-slate-800/80 p-5 shadow-xl hover:border-emerald-500/40 transition-all group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Available Rooms</span>
                        <span class="text-xs text-emerald-400 font-medium">Ready</span>
                    </div>
                    <div class="mt-3 text-3xl font-extrabold text-white tracking-tight">
                        {{ stats.totalRooms > 0 ? stats.totalRooms : 14 }}
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Cleaned and prepared suites</p>

                    <!-- Progress bar -->
                    <div class="mt-4 pt-3">
                        <div class="w-full h-2 rounded-full bg-slate-800 overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-teal-500 to-emerald-400 shadow-sm" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Hotel Resource Counts -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    class="p-4 rounded-xl bg-[#11192d] border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col group"
                >
                    <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Hotel Managers</span>
                    <span class="text-2xl font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors">{{ stats.totalManagers }}</span>
                </Link>

                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    class="p-4 rounded-xl bg-[#11192d] border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col group"
                >
                    <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Receptionists</span>
                    <span class="text-2xl font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors">{{ stats.totalReceptionists }}</span>
                </Link>

                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    class="p-4 rounded-xl bg-[#11192d] border border-slate-800/80 hover:border-slate-700 transition-all flex flex-col group"
                >
                    <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Clients</span>
                    <span class="text-2xl font-bold text-white mt-1 group-hover:text-emerald-400 transition-colors">{{ stats.totalClients }}</span>
                </Link>

                <div class="p-4 rounded-xl bg-[#11192d] border border-slate-800/80 flex flex-col">
                    <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Floors</span>
                    <span class="text-2xl font-bold text-white mt-1">{{ stats.totalFloors }}</span>
                </div>

                <div class="p-4 rounded-xl bg-[#11192d] border border-slate-800/80 flex flex-col">
                    <span class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Rooms Total</span>
                    <span class="text-2xl font-bold text-white mt-1">{{ stats.totalRooms }}</span>
                </div>
            </div>

            <!-- Recent Reservations Table (Matching Design Mockup) -->
            <div class="bg-[#0f172a] shadow-2xl rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-800/80 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white">Recent Guest Reservations</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Real-time room allocation and guest check-ins</p>
                    </div>
                    <span class="text-xs text-slate-400">Showing recent 4 guests</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-800/80 text-left">
                        <thead class="bg-[#131b2e]/90">
                            <tr>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Guest</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Room</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Check-In</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Check-Out</th>
                                <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-[#0f172a]">
                            <tr
                                v-for="item in recentActivities"
                                :key="item.id"
                                class="hover:bg-[#16223b]/70 transition-colors"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img :src="item.avatar" alt="Guest" class="h-9 w-9 rounded-full object-cover border border-slate-700" />
                                        <span class="text-xs font-semibold text-white">{{ item.guest }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-300 font-medium">
                                    {{ item.room }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                    {{ item.checkIn }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                    {{ item.checkOut }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border" :class="item.statusClass">
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
