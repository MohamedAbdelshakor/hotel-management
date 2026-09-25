<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl bg-[#131b2e] border border-slate-700/80 px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-slate-300 transition-all duration-150 hover:bg-slate-800 hover:text-white hover:border-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-700 active:scale-95 disabled:opacity-40 shadow-sm"
    >
        <slot />
    </button>
</template>
