<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AppLayout from '@/Layouts/AppLayout'
import Form from './Form'
import Breadcrumbs from '@/Shared/Breadcrumbs'

const props = defineProps(['type', 'title', 'description', 'model', 'variables']);

const getCrumbs = () => {
    return [
        {
            title: 'Users',
            href: route('user.index')
        },
        {
            title: props.type === 'POST' ? 'Create' : 'Edit',
            href: null
        },
    ]
}

const onSuccess = () => {
    // ...
}

</script>

<template>
    <Head title="User Management" />

    <div>
        <AppLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
                    {{ title }}
                </h2>
            </template>

            <Breadcrumbs :links="getCrumbs()" />

            <div>
                <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
                    <Form
                        :type="type"
                        :title="title"
                        :description="description"
                        :model="model"
                        @success="onSuccess"
                    />
                </div>
            </div>
        </AppLayout>
    </div>
</template>
