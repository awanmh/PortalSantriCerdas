<script setup>
import { ref } from "vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav class="bg-green-800 border-b border-green-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <img src="/images/logo.png" alt="Logo" class="block h-10 w-auto rounded-full">
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="text-gray-200 hover:text-white focus:text-white focus:border-green-300">
                                    Dashboard
                                </NavLink>
                                <NavLink :href="route('jadwal.index')" :active="route().current('jadwal.index')" class="text-gray-200 hover:text-white focus:text-white focus:border-green-300">
                                    Jadwal Pelajaran
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div v-if="$page.props.auth && $page.props.auth.user" class="flex items-center gap-4 text-sm">
                                <span class="font-medium text-white">{{ $page.props.auth.user.name }}</span>
                                <span class="text-gray-400">|</span>
                                <Link :href="route('profile.edit')" class="text-gray-300 hover:text-white transition duration-150 ease-in-out">
                                    Profile
                                </Link>
                                <Link :href="route('logout')" method="post" as="button" class="text-gray-300 hover:text-white transition duration-150 ease-in-out">
                                    Log Out
                                </Link>
                            </div>
                        </div>


                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-300 hover:text-white hover:bg-green-700 focus:outline-none focus:bg-green-700 focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('jadwal.index')" :active="route().current('jadwal.index')"> Jadwal Pelajaran </ResponsiveNavLink>
                    </div>

                    <div v-if="$page.props.auth && $page.props.auth.user" class="pt-4 pb-1 border-t border-green-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-white">{{ $page.props.auth.user.name }}</div>
                            <div class="font-medium text-sm text-gray-300">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button"> Log Out </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>