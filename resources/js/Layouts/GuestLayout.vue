<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex antialiased font-sans">
        <!-- Left Showcase Side (Desktop Only) -->
        <div class="hidden lg:flex lg:w-1/2 bg-slate-900 relative flex-col justify-between p-12 text-white overflow-hidden">
            <!-- Subtle Architectural Background Layer -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 opacity-95"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#334155_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>

            <!-- Top Brand -->
            <div class="relative z-10">
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="h-11 w-11 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:scale-105 transition-transform shadow-inner">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-lg font-bold tracking-tight text-white flex items-center gap-2">
                            Grand Horizon
                            <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Hotel & Suites</span>
                        </div>
                        <div class="text-xs text-slate-400">Management & Reservation Portal</div>
                    </div>
                </Link>
            </div>

            <!-- Middle Value Proposition -->
            <div class="relative z-10 max-w-lg space-y-6 my-auto py-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-slate-800 border border-slate-700 text-amber-300">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                    Hospitality Management Suite
                </div>

                <h2 class="text-3xl font-extrabold tracking-tight text-white leading-tight">
                    Refined luxury management for exceptional guest experiences.
                </h2>

                <p class="text-sm text-slate-300 leading-relaxed">
                    Access real-time guest reservations, room inventory, automated check-ins, and multi-tier staff coordination in one intuitive workspace.
                </p>

                <!-- Feature bullet pills -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="flex items-center gap-2.5 text-xs text-slate-300 bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/50">
                        <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Live Room Allocations</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-300 bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/50">
                        <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Front-Desk Approvals</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-300 bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/50">
                        <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Role-Based Security</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-300 bg-slate-800/60 p-2.5 rounded-lg border border-slate-700/50">
                        <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Instant Stripe Checkout</span>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="relative z-10 flex items-center justify-between text-xs text-slate-400 pt-6 border-t border-slate-800">
                <span>Grand Horizon Luxury Hospitality</span>
                <span>Version 1.0</span>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-6 sm:p-12 lg:p-16">
            <!-- Mobile Brand Header -->
            <div class="lg:hidden mb-8 text-center">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-slate-900 text-amber-400 shadow-md mb-3">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-900">Grand Horizon Hotel</h1>
                <p class="text-xs text-slate-500">Management & Reservation Portal</p>
            </div>

            <!-- Form Card Box -->
            <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-2xl shadow-xl shadow-slate-200/40 p-8 sm:p-10">
                <slot />
            </div>

            <div class="mt-8 text-center text-xs text-slate-400">
                Protected by enterprise authentication and role-based policies.
            </div>
        </div>
    </div>
</template>
