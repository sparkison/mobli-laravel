<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import {defineProps, ref} from 'vue'

defineProps({
    actions: Array
})

const buttonClass = ref('inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring focus:ring-gray-200 focus:ring-opacity-50')

</script>

<template>
    <span>
        <template v-if="actions.length > 1">
            <Dropdown align="left" width="60">
                <template #trigger>
                    <div class="cursor-pointer text-gray-500 rounded-full h-8 w-8 flex items-center justify-center border border-transparent bg-none hover:bg-gray-100 hover:border-gray-200 hover:bg-gray-200">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </div>
                </template>
                <template #content>
                    <div class="w-60 text-left">
                        <div class="block text-xs text-gray-400"
                             v-for="(action, index) in actions">
                            <DropdownLink
                                v-if="action.action"
                                as="button"
                                @click.native="action.action"
                            >
                                {{ action.title }}
                            </DropdownLink>
                            <DropdownLink v-else :href="action.href">
                                {{ action.title }}
                            </DropdownLink>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </template>
        <template v-else-if="actions.length === 1">
            <Link
                :href="actions[0].href"
                :class="[buttonClass, 'rounded-full']">
                {{ actions[0].title }}
            </Link>
        </template>
    </span>
</template>
