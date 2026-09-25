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
        class="block w-full px-4 py-2 text-start text-xs font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-slate-900 focus:bg-slate-50 focus:outline-none"
    >
        <slot />
    </Link>
</template>
