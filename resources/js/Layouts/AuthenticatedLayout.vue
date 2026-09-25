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
    if (user.value.is_client) return 'Guest / Client';
    return 'Staff';
});

const roleBadgeClass = computed(() => {
    if (user.value.is_admin) return 'bg-amber-50 text-amber-700 border-amber-200';
    if (user.value.is_manager) return 'bg-indigo-50 text-indigo-700 border-indigo-200';
    if (user.value.is_receptionist) return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    return 'bg-slate-100 text-slate-700 border-slate-200';
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800 flex antialiased font-sans">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 shrink-0 bg-white border-r border-slate-200 sticky top-0 h-screen z-30">
            <!-- Brand Logo -->
            <div class="h-16 px-6 flex items-center border-b border-slate-200 gap-3">
                <div class="h-9 w-9 rounded-lg bg-slate-900 flex items-center justify-center text-amber-400 shrink-0 shadow-sm">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 tracking-tight">
                        Grand Horizon
                    </h1>
                    <p class="text-[11px] text-slate-500 font-medium">Hotel Management</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">Operations</div>

                <!-- Dashboard -->
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('dashboard')
                        ? 'bg-slate-100 text-slate-900 font-semibold border-l-2 border-slate-900'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </Link>

                <!-- Manage Managers (Admin Only) -->
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('admin.managers.*')
                        ? 'bg-slate-100 text-slate-900 font-semibold border-l-2 border-slate-900'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Managers</span>
                </Link>

                <!-- Manage Receptionists (Admin & Manager) -->
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('receptionists.*')
                        ? 'bg-slate-100 text-slate-900 font-semibold border-l-2 border-slate-900'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Receptionists</span>
                </Link>

                <!-- Manage Clients (Admin & Manager) -->
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    :class="route().current('clients.*')
                        ? 'bg-slate-100 text-slate-900 font-semibold border-l-2 border-slate-900'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                >
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Clients</span>
                </Link>

                <div class="pt-4 text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 mb-2">Hospitality</div>

                <!-- Floors (Stage 6) -->
                <div class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Floors</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Stage 6</span>
                </div>

                <!-- Rooms (Stage 7) -->
                <div class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Rooms</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Stage 7</span>
                </div>

                <!-- Reservations (Stage 9) -->
                <div class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Reservations</span>
                    </div>
                    <span class="text-[10px] font-medium bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Stage 9</span>
                </div>
            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="p-3.5 border-t border-slate-200 bg-slate-50/60">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <img
                            :src="user.avatar_url || 'https://ui-avatars.com/api/?name=User&background=0f172a&color=fff'"
                            alt="Avatar"
                            class="h-8 w-8 rounded-full object-cover border border-slate-300 shrink-0"
                        />
                        <div class="min-w-0">
                            <div class="text-xs font-semibold text-slate-900 truncate">{{ user.name }}</div>
                            <div class="text-[10px] text-slate-500 truncate">{{ roleTitle }}</div>
                        </div>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-md transition-colors shrink-0"
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
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 lg:hidden"
            @click="mobileMenuOpen = false"
        />

        <!-- Mobile Sidebar Drawer -->
        <div
            class="fixed inset-y-0 left-0 w-64 bg-white z-50 transform transition-transform duration-300 ease-in-out lg:hidden flex flex-col border-r border-slate-200 shadow-xl"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="h-16 px-6 flex items-center justify-between border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-slate-900 flex items-center justify-center text-amber-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="font-bold text-sm text-slate-900">Grand Horizon</span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-1 text-slate-400 hover:text-slate-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1">
                <Link
                    :href="route('dashboard')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Dashboard
                </Link>
                <Link
                    v-if="user.is_admin"
                    :href="route('admin.managers.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Managers
                </Link>
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('receptionists.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Receptionists
                </Link>
                <Link
                    v-if="user.is_admin || user.is_manager"
                    :href="route('clients.index')"
                    @click="mobileMenuOpen = false"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Clients
                </Link>
            </div>
        </div>

        <!-- Main Layout Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar Header -->
            <header class="sticky top-0 z-20 h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between gap-4 shadow-2xs">
                <div class="flex items-center gap-3 flex-1 max-w-md">
                    <!-- Mobile Hamburger -->
                    <button
                        @click="mobileMenuOpen = true"
                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 lg:hidden"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Global Search Bar -->
                    <div class="relative w-full hidden sm:block">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Quick search guests, rooms, staff..."
                            class="w-full pl-9 pr-3.5 py-1.5 text-xs rounded-lg bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 transition-colors"
                        />
                    </div>
                </div>

                <!-- Topbar Actions & User Dropdown -->
                <div class="flex items-center gap-3">
                    <!-- Role Badge -->
                    <div class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border" :class="roleBadgeClass">
                        {{ roleTitle }}
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    type="button"
                                    class="flex items-center gap-2 p-1 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none"
                                >
                                    <img
                                        :src="user.avatar_url || 'https://ui-avatars.com/api/?name=User&background=0f172a&color=fff'"
                                        alt="Avatar"
                                        class="h-8 w-8 rounded-full object-cover border border-slate-200"
                                    />
                                    <span class="hidden md:block text-xs font-semibold text-slate-700 text-left">
                                        {{ user.name }}
                                    </span>
                                    <svg class="h-3.5 w-3.5 text-slate-400 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <div class="px-4 py-2 border-b border-slate-100 text-xs text-slate-500">
                                    Signed in as <strong class="text-slate-900">{{ user.email }}</strong>
                                </div>
                                <DropdownLink :href="route('profile.edit')">
                                    Profile Settings
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="text-rose-600 hover:bg-rose-50 hover:text-rose-700">
                                    Sign Out
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page Header Slot -->
            <div v-if="$slots.header" class="border-b border-slate-200 bg-white px-4 sm:px-8 py-5">
                <slot name="header" />
            </div>

            <!-- Main Page Content -->
            <main class="flex-1 px-4 sm:px-8 py-8">
                <slot />
            </main>
        </div>
    </div>
</template>
