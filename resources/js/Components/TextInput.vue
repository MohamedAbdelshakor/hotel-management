<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-xl bg-[#131b2e] border border-slate-700/70 text-slate-100 placeholder-slate-500 shadow-inner focus:border-emerald-500/70 focus:ring-2 focus:ring-emerald-500/30 text-sm transition-all"
        v-model="model"
        ref="input"
    />
</template>
