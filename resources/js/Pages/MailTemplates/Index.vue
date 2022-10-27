<script setup>
import { Link } from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import {
    Table,
    TableActionButton
} from '@/Components/Table'
import Notice from '@/Components/Notice.vue'
import useTableFunctions from '../../Components/Table/useTableFunctions.js'
import {ref, toRef} from 'vue'

const props = defineProps(['models', 'create_url', 'sortable'])

/* Optional - add extra table features */
const data = toRef(props, 'models')
const tableData = ref({
    data: data.value?.data,
    perPage: data.value?.per_page,
    total: data.value?.total,
})
const {
    // ref()
    allSelected,
    selected,
    // fn()
    getModelValue,
    toggleAll,
    toggleRow,
    isRowSelected
} = useTableFunctions(tableData)

</script>
<template>
    <AppLayout title="Email Template Management">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Notifications
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-xl sm:rounded-lg">
                <template v-if="!models.data.length">
                    <Notice type="info">
                        <template #title>
                            No templates found
                        </template>
                        <template #description>
                            Create a new template to get started
                        </template>
                    </Notice>
                </template>
                <template v-else>
                    <Table :resource="models">
                        <template #cell(actions)="{ item: model }">
                            <TableActionButton
                                :actions="[
                                    {href: route('notifications.edit', model.id), title: 'Edit'},
                                    {href: route('notifications.show', model.id), title: 'Preview'},
                                ]"
                            />
                        </template>
                        <template #cell(name)="{ item: model }">
                            <Link :href="route('notifications.edit', model.id)" class="text-primary-400 hover:text-primary-700">
                                {{ model.name }}
                            </Link>
                        </template>
                    </Table>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
