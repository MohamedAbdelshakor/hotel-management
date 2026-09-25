<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const mobileMenuOpen = ref(false);

const roleTitle = computed(() => {
    if (user.value.is_admin) return 'Administrator';
    if (user.value.is_manager) return 'General Manager';
    if (user.value.is_receptionist) return 'Receptionist';
    if (user.value.is_client) return 'Valued Client';
    return 'Staff';
});

const roleBadgeColor = computed(() => {
    if (user.value.is_admin) return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
    if (user.value.is_manager) return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
    if (user.value.is_receptionist) return 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20';
    return 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
});
</script>

<template>
    <div class="min-h-screen bg-[#0b0f19] text-slate-100 flex antialiased font-sans">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-[#0d1424] border-r border-slate-800/80 sticky top-0 h-screen z-30">
            <!-- Brand Logo -->
            <div class="h-20 px-6 flex items-center border-b border-slate-800/80 gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-400 via-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-950/40 shrink-0">
                    <svg class="h-6 w-6 text-slate-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold tracking-tight text-white flex items-center gap-1.5">
                        Grand Horizon
                        <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Hotel</span>
                    </h1>
                    <p class="text-[11px] text-slate-400">Luxury & Suites</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-5 space-y-1.5">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 mb-2">Main Menu</div>

                <!-- Dashboard -->
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all group"
                    :class="route().current('dashboard')
                        ? 'bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent text-emerald-400 border-l-4 border-emerald-500 font-semibold shadow-sm'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                >
                    <svg class="h-5 w-5 shrink-0 transition-colors" :class="route().current('dashboard') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </Link>

                <!-- Manage Managers (Admin Only) -->
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all group"
                    :class="route().current('admin.managers.*')
                        ? 'bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent text-emerald-400 border-l-4 border-emerald-500 font-semibold shadow-sm'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                >
                    <svg class="h-5 w-5 shrink-0 transition-colors" :class="route().current('admin.managers.*') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Managers</span>
                </Link>

                <!-- Manage Receptionists (Admin & Manager) -->
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all group"
                    :class="route().current('receptionists.*')
                        ? 'bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent text-emerald-400 border-l-4 border-emerald-500 font-semibold shadow-sm'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                >
                    <svg class="h-5 w-5 shrink-0 transition-colors" :class="route().current('receptionists.*') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Receptionists</span>
                </Link>

                <!-- Manage Clients (Admin & Manager) -->
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all group"
                    :class="route().current('clients.*')
                        ? 'bg-gradient-to-r from-emerald-500/15 via-emerald-500/5 to-transparent text-emerald-400 border-l-4 border-emerald-500 font-semibold shadow-sm'
                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                >
                    <svg class="h-5 w-5 shrink-0 transition-colors" :class="route().current('clients.*') ? 'text-emerald-400' : 'text-slate-400 group-hover:text-slate-300'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Clients</span>
                </Link>

                <div class="pt-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400 px-3 mb-2">Hotel Operations</div>

                <!-- Floors (Future Stage 6) -->
                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-800/40 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Floors</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Stage 6</span>
                </div>

                <!-- Rooms (Future Stage 7) -->
                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-800/40 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Rooms</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Stage 7</span>
                </div>

                <!-- Reservations (Future Stage 9) -->
                <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-400 hover:text-slate-300 hover:bg-slate-800/40 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-slate-400 group-hover:text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Reservations</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-800 text-slate-400 px-2 py-0.5 rounded-full">Stage 9</span>
                </div>
            </div>

            <!-- Sidebar Footer User Profile -->
            <div class="p-4 border-t border-slate-800/80 bg-[#0a0f1c]/50">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img
                            :src="user.avatar_url || 'https://ui-avatars.com/api/?name=User&background=10b981&color=fff'"
                            alt="Avatar"
                            class="h-9 w-9 rounded-full object-cover border border-slate-700 shrink-0"
                        />
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-white truncate">{{ user.name }}</div>
                            <div class="text-[11px] text-slate-400 truncate">{{ roleTitle }}</div>
                        </div>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-2 text-slate-400 hover:text-red-400 hover:bg-slate-800/60 rounded-lg transition-colors shrink-0"
                        title="Sign out"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Overlay -->
        <div
            v-if="mobileMenuOpen"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 lg:hidden"
            @click="mobileMenuOpen = false"
        />

        <!-- Mobile Sidebar Drawer -->
        <div
            class="fixed inset-y-0 left-0 w-72 bg-[#0d1424] z-50 transform transition-transform duration-300 ease-in-out lg:hidden flex flex-col border-r border-slate-800"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-amber-400 via-emerald-500 to-teal-600 flex items-center justify-center">
                        <svg class="h-5 w-5 text-slate-950" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="font-bold text-white">Grand Horizon</span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
                <Link
                    :href="route('dashboard')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800"
                >
                    Dashboard
                </Link>
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800"
                >
                    Manage Managers
                </Link>
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800"
                >
                    Manage Receptionists
                </Link>
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800"
                >
                    Manage Clients
                </Link>
            </div>
        </div>

        <!-- Main Layout Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar Header -->
            <header class="sticky top-0 z-20 h-20 bg-[#0d1424]/80 backdrop-blur-xl border-b border-slate-800/80 px-4 sm:px-8 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-1 max-w-xl">
                    <!-- Mobile Hamburger -->
                    <button
                        @click="mobileMenuOpen = true"
                        class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 lg:hidden"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Global Search Bar -->
                    <div class="relative w-full max-w-md hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Search guests, rooms, reservations..."
                            class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-[#131b2e] border border-slate-700/60 text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/60 transition-all shadow-inner"
                        />
                    </div>
                </div>

                <!-- Topbar Actions & User Dropdown -->
                <div class="flex items-center gap-4">
                    <!-- Quick Notification Bell -->
                    <button class="relative p-2 rounded-xl text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-emerald-400 ring-2 ring-[#0d1424]"></span>
                    </button>

                    <!-- Role Badge -->
                    <div class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border" :class="roleBadgeColor">
                        {{ roleTitle }}
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center gap-2.5 p-1 rounded-full hover:ring-2 hover:ring-emerald-500/30 transition-all focus:outline-none"
                                >
                                    <img
                                        :src="user.avatar_url || 'https://ui-avatars.com/api/?name=User&background=10b981&color=fff'"
                                        alt="Avatar"
                                        class="h-9 w-9 rounded-full object-cover border border-slate-700"
                                    />
                                    <span class="hidden md:block text-xs font-semibold text-slate-200 text-left">
                                        {{ user.name }}
                                    </span>
                                    <svg class="h-4 w-4 text-slate-400 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 border-b border-slate-800 text-xs text-slate-400">
                                    Signed in as <strong class="text-white">{{ user.email }}</strong>
                                </div>
                                <DropdownLink :href="route('profile.edit')" class="text-slate-300 hover:bg-slate-800 hover:text-white">
                                    Profile Settings
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-400 hover:bg-red-500/10 hover:text-red-300">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page Header (Optional Slot) -->
            <div v-if="$slots.header" class="border-b border-slate-800/60 bg-[#0d1424]/40 px-4 sm:px-8 py-5">
                <slot name="header" />
            </div>

            <!-- Main Page Content -->
            <main class="flex-1 px-4 sm:px-8 py-8">
                <slot />
            </main>
        </div>
    </div>
</template>
