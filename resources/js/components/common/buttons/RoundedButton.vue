<script setup>
import BaseButton from '@/components/common/buttons/BaseButton.vue';

defineProps({
    to: String,
    text: String,
    disabled: Boolean,
    loading: Boolean,
    variant: String
});

const styles = {
    default: 'text-white border-neutral-400 hover:bg-slate-50 hover:text-zinc-950',
    disabled: 'text-white bg-neutral-500 text-neutral-200 border-transparent cursor-not-allowed',
    fill: 'text-zinc-950 bg-slate-50 border-transparent hover:bg-indigo-500 hover:text-white',
    "fill-red": 'text-white bg-red-500 border-transparent hover:bg-red-600 active:bg-red-400',
    loading: 'border-neutral-600 cursor-progress'
}
</script>

<template>
    <BaseButton
        v-bind='$props'
        class='flex justify-center w-fit h-fit border py-1 px-4 rounded-full text-sm text-nowrap font-semibold transition-all duration-200'
        :class="styles[disabled ? 'disabled' : loading ? 'loading' : variant ?? 'default'] ?? styles.default"
        :disabled='disabled'
    />
</template>