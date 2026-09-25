<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
})
</script>

<template>
    <Head title="Welcome to Grand Horizon Hotel & Suites" />

    <div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans">
        <!-- Top Navigation Bar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Hotel Brand -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-white font-bold text-base shadow-xs">
                        GH
                    </div>
                    <div>
                        <span class="text-base font-bold tracking-tight text-slate-900 block leading-tight">Grand Horizon</span>
                        <span class="text-[11px] font-medium tracking-wider uppercase text-slate-400 block leading-none">Property Management System</span>
                    </div>
                </div>

                <!-- Navigation Action Links -->
                <nav v-if="canLogin" class="flex items-center space-x-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-colors shadow-2xs"
                    >
                        Access Management Console &rarr;
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors shadow-2xs"
                        >
                            Staff Sign In
                        </Link>

                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-colors shadow-2xs"
                        >
                            Register Guest Account
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16 w-full flex flex-col justify-center">
            <!-- Hero Section -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-4 shadow-2xs">
                    Hotel Hospitality System Online
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    Elevating Guest Hospitality & Operations
                </h1>
                <p class="mt-4 text-base sm:text-lg text-slate-600 leading-relaxed">
                    A centralized enterprise platform engineered for seamless front-desk check-ins, multi-floor inventory, and personalized guest services.
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-2xs transition-colors"
                    >
                        Open Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 rounded-lg shadow-2xs transition-colors"
                        >
                            Sign In to Portal
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 rounded-lg shadow-2xs transition-colors"
                        >
                            Create Guest Profile
                        </Link>
                    </template>
                </div>
            </div>

            <!-- Core Operations Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <!-- Card 1: Front Desk & Reception -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-7 shadow-2xs">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Reception & Front Desk</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Effortless check-ins, guest verification, key-card assignments, and concierge requests handled instantly with zero friction.
                    </p>
                </div>

                <!-- Card 2: Floor & Room Inventory -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-7 shadow-2xs">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Multi-Floor Room Inventory</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Real-time floor status, room availability, housekeeping updates, and capacity distribution across high-rise hospitality levels.
                    </p>
                </div>

                <!-- Card 3: Governance & Security -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 sm:p-7 shadow-2xs">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 mb-1.5">Role-Based Access Control</h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Strict permission isolation for System Admins, Floor Managers, Receptionists, and Clients with audited access logs.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <p>&copy; 2026 Grand Horizon Hotel & Suites. All rights reserved.</p>
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center text-emerald-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Secure System
                    </span>
                    <span>Version 1.0.0</span>
                </div>
            </div>
        </footer>
    </div>
</template>
