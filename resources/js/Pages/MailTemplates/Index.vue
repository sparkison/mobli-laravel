<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-400">
                Email Templates
            </h2>
        </template>

        <div class="">
            <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-xl sm:rounded-lg">
                    <tw-table>
                        <template #head>
                            <tr>
                                <th scope="col"
                                    class="relative px-3 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                                <th scope="col"
                                    class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject
                                </th>
                            </tr>
                        </template>
                        <template #body>
                            <tr v-for="template in templates.data">
                                <td class="px-3">
                                    <table-action-button
                                        :actions="[
                                            {href: template.edit_url, title: 'Edit'},
                                            {href: template.preview_url, title: 'Preview'},
                                        ]"
                                    />
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <inertia-link :href="template.edit_url">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                            {{ template.name }}
                                        </div>
                                        <div v-if="template.info" class="max-w-md whitespace-normal">
                                            <p class="text-gray-400 text-xs">
                                                {{ template.info }}
                                            </p>
                                        </div>
                                    </inertia-link>
                                </td>
                                <td class="px-2 py-2 whitespace-nowrap">
                                    <div class="text-sm max-w-md truncate font-medium text-gray-900 dark:text-gray-200">
                                        {{ template.subject }}
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tw-table>
                    <pagination :links="templates.links" />
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import Pagination from '@/Shared/Pagination'
import TableActionButton from '@/Shared/TableActionButton'
import Table from '@/Shared/Table'
import UrlButton from '@/Shared/UrlButton'
import JetNotice from '@/Jetstream/Notice'

export default {
    props: ['templates', 'create_url'],

    components: {
        AppLayout,
        Pagination,
        TableActionButton,
        UrlButton,
        JetNotice,
        'tw-table': Table
    },
}
</script>
