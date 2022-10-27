<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue'
import {createPopper} from '@popperjs/core'

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentRingClasses: {
        type: Array,
        default: () => ['ring-1', 'ring-black', 'ring-opacity-5']
    },
    contentClasses: {
        type: Array,
        default: () => ['py-1', 'rounded-md', 'bg-white'],
    },
    contentWrapperClasses: {
        type: Array,
        default: () => ['mt-2', 'rounded-md', 'shadow-lg'],
    },
    triggerClasses: {
        type: Array,
        default: () => [],
    },
})

const open = ref(false)
const popperInstance = ref(null)
const dropdownButton = ref(null)
const dropdownContent = ref(null)

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false
    }
}

const close = (e) => {
    open.value = false
}
const toggle = () => {
    open.value = !open.value
    popperInstance.value?.update()
}

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape)
    popperInstance.value = createPopper(dropdownButton.value, dropdownContent.value, {
        placement: 'bottom-start',
        strategy: 'fixed',
        modifiers: [
            {
                name: 'offset',
                options: {
                    offset: [0, 0],
                },
            },
        ],
    })
})
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape)
    if (popperInstance.value) {
        popperInstance.value?.destroy()
        popperInstance.value = null
    }
})

const widthClass = computed(() => {
    return {
        '48': 'w-48',
        '60': 'w-60',
        'full': 'w-full',
    }[props.width.toString()]
})

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0'
    }

    if (props.align === 'right') {
        return 'origin-top-right right-0'
    }

    return 'origin-top'
})
</script>

<template>
    <div class="relative">
        <div @click="toggle" :class="triggerClasses" ref="dropdownButton">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40" @click="close" />

        <div class="z-50" :class="{'w-full': width === 'full'}" ref="dropdownContent">
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
            >
                <div v-show="open"
                     :class="[widthClass, alignmentClasses, contentWrapperClasses]"
                     @click="close">
                    <div :class="[contentRingClasses, contentClasses]">
                        <slot name="content" />
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>
