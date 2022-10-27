<template>
    <div>
        <app-layout title="User Management">
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Users
                </h2>
            </template>

            <breadcrumbs :links="getCrumbs()" />

            <div>
                <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <url-button :href="route('user.create')" class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create New
                    </url-button>
                    <inertia-table
                        :filters="queryBuilderProps.filters"
                        :search="queryBuilderProps.search"
                        :columns="queryBuilderProps.columns"
                        :per-page="queryBuilderProps.perPage"
                        :on-update="setQueryBuilder"
                        :meta="models"
                    >
                        <template #head>
                            <tr>
                                <th class="sticky top-0 bg-gray-100"></th>
                                <th v-for="column in queryBuilderProps.columns"
                                    class="sticky top-0 bg-gray-100"
                                    :class="{'cursor-pointer hover:bg-gray-200': sortable.includes(column.key)}"
                                    v-show="showColumn(column.key)">
                                    <span v-if="sortable.includes(column.key)"
                                          @click.prevent="sortBy(column.key)">
                                        <column-sort
                                            :sort-key="column.key"
                                            :sort="queryBuilderProps.sort"
                                            :sortable="true"
                                        >
                                            {{ column.label }}
                                        </column-sort>
                                    </span>
                                    <span v-else class="text-left py-1 inline-flex items-center text-xs font-medium text-gray-500 uppercase">
                                        {{ column.label }}
                                    </span>
                                </th>
                            </tr>
                        </template>

                        <template #body>
                            <tr v-for="model in models.data"
                                :key="model.id"
                                class="text-gray-500 bg-white hover:bg-gray-50">
                                <td>
                                    <table-action-button :actions="model.id === $page.props.user.id ? [
                                        {href: route('profile.show'), title: 'Profile'},
                                    ] : [
                                        {href: route('user.edit', model.id), title: 'Edit'},
                                        {href: route('impersonate', model.id), title: 'Impersonate'},
                                        {href: null, title: null, divider: true},
                                        {action: () => this.destroy(model), title: 'Delete'},
                                    ]" />
                                </td>
                                <td v-for="column in queryBuilderProps.columns"
                                    v-show="showColumn(column.key)">
                                    <inertia-link
                                        v-if="column.key === 'name' && model.id === $page.props.user.id"
                                        :href="route('profile.show')"
                                        class="text-blue hover:text-blue-600 hover:font-medium">
                                        {{ model[column.key] }} <span class="text-xs font-bold">(You)</span>
                                    </inertia-link>
                                    <inertia-link
                                        v-else-if="column.key === 'name'"
                                        :href="route('user.edit', model.id)"
                                        class="text-blue hover:text-blue-600 hover:font-medium">
                                        {{ model[column.key] }}
                                    </inertia-link>
                                    <span v-else v-html="getModelValue(model, column.key)" />
                                </td>
                            </tr>
                        </template>
                    </inertia-table>
                </div>
            </div>
        </app-layout>
    </div>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Shared/Breadcrumbs';
import {Tailwind2} from '@/Shared/Table'
import TableActionButton from '@/Shared/TableActionButton'
import ColumnSort from '@/Shared/ColumnSort'
import UrlButton from '@/Shared/UrlButton'
import { InteractsWithQueryBuilder } from '@/Shared/Table';
import TableMixin from '@/Mixins/TableMixin'

export default {
    props: {
        models: Object,
        sortable: Array
    },
    components: {
        AppLayout,
        Breadcrumbs,
        UrlButton,
        TableActionButton,
        ColumnSort,
        'inertia-table': Tailwind2.Table
    },
    mixins: [InteractsWithQueryBuilder, TableMixin],

    data: () => ({
        // ...
    }),

    methods: {
        getCrumbs () {
            return [
                { title: 'Users', href: null },
            ]
        },
        destroy (model) {
            this.$swal().fire({
                title: 'Please confirm',
                html: `Delete user ${model.email}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$inertia.delete(route('user.destroy', model.id), {
                        preserveScroll: false,
                        onSuccess: () => {},
                        onError: (error) => { this.$toast().error(Object.values(error).join(" ")) }
                    })
                }
            })
        }
    }
}
</script>
