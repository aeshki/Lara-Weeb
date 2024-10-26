<script setup>
import { Search } from 'lucide-vue-next';
import BaseInput from './BaseInput.vue';

defineProps({
    id: String,
    label: String,
    placeholder: String,
    minLength: {
        type: [String, Number],
        default: 2
    },
    maxLength: {
        type: [String, Number],
        default: 2000
    },
    invalid: Boolean,
    required: Boolean,
    disabled: Boolean,
    error: String
});

const change = defineModel();
const emit = defineEmits([ 'validate' ]);

const styles = {
  default: 'outline-neutral-600 focus:outline-neutral-500 focus:outline-offset-2 hover:outline-neutral-400',
  invalid: 'outline-red-900 focus:outline-red-700 focus:outline-offset-2 hover:outline-red-600',
  disabled: 'bg-zinc-800 text-neutral-400 outline-neutral-600',
};

const handleKeyPress = () => {
    emit("validate", change);
};
</script>

<template>
    <BaseInput :error='error'>
        <div class='flex flex-col gap-2 text-white'>
            <label
                class='font-semibold uppercase text-sm'
                :class="required ? `after:content-['*'] after:ml-1 after:text-red-600` : ''"
                :id='id'
            >{{ label }}</label>
            <div
                class='flex items-center outline outline-1 pr-2 rounded-sm transition-all duration-100'
                :class="styles[invalid || error ? 'invalid' : disabled ? 'disabled' : 'default']"
            >
                <input
                    :id='id'
                    type='search'
                    :required='required'
                    :disabled='disabled'
                    :placeholder='placeholder'
                    :maxlength='maxLength'
                    :minlength='minLength'
                    class='bg-transparent p-2 text-sm outline-none'
                    autocomplete='off'
                    v-model='change' 
                    @keypress.enter='handleKeyPress()'
                />
                <Search
                    class='text-white'
                    size='21'
                    @click='emit("validate", change)'
                />
            </div>
        </div>
    </BaseInput>
</template>