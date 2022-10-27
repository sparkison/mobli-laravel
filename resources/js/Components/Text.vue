<script setup>
const props = defineProps(['modelValue', 'rows'])
const emit = defineEmits(['update:modelValue'])

const getRows = () => {
    if (props.rows.value) {
        return parseInt(props.rows.value)
    }
    return 3
}
</script>
<template>
    <textarea :rows="getRows()" class="nightwind-prevent text-gray-700 text-lg shadow-sm block w-full py-2.5 focus:ring focus:ring-primary-200 focus:ring-opacity-50 sm:text-sm border-gray-300 rounded-md shadow-sm" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" ref="input"></textarea>
</template>

