<script setup>
import {watch} from 'vue'

const props = defineProps(['modelValue', 'value', 'name', 'disabled'])
const emit = defineEmits(['update:modelValue', 'toggled'])

const toggled = ref(props.modelValue.value || props.value.value)

watch(value, (newVal) => {
    toggled.value = newVal
})

const toggle = () => {
    toggled.value = !toggled.value
    emit('toggled')
    emit('update:modelValue', toggled.value)
}
</script>
<template>
    <div class="flex flex-row items-center">
        <button type="button"
                :disabled="disabled"
                @click="toggle"
                :aria-pressed="toggled"
                :class="{
                    'bg-primary-600': toggled,
                    'bg-gray-300': !toggled,
                    'cursor-not-allowed': disabled
                }"
                class="mt-1 bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
            <span class="sr-only">{{ name || 'Toggle' }}</span>
            <span aria-hidden="true"
                  :class="{
                      'translate-x-5': toggled,
                      'translate-x-0': !toggled
                  }"
                  class="translate-x-0 inline-block h-5 w-5 rounded-full bg-white dark:bg-gray-900 shadow transform ring-0 transition ease-in-out duration-200"></span>
        </button>
        <p v-if="name" class="text-sm font-medium text-gray-500 ml-2 mt-1">
            {{ name }}
        </p>
        <slot></slot>
    </div>
</template>
