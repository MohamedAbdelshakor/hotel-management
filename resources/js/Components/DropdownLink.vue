<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Link
        :href="href"
        class="block w-full px-4 py-2 text-start text-xs font-medium text-slate-300 transition duration-150 ease-in-out hover:bg-slate-800/80 hover:text-white focus:bg-slate-800 focus:outline-none"
    >
        <slot />
    </Link>
</template>
