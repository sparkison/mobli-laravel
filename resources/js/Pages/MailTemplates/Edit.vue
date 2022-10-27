<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import {useForm} from '@inertiajs/inertia-vue3'
import AppLayout from '@/Layouts/AppLayout'
import ActionMessage from '@/Components/ActionMessage'
import Button from '@/Components/Button'
import FormSection from '@/Components/FormSection'
import Input from '@/Components/Input'
import Text from '@/Components/Text'
import RichText from '@/Components/RichText'
import InputError from '@/Components/InputError'
import Label from '@/Components/Label'
import Dropdown from '@/Components/Dropdown'
import {defineProps} from 'vue'

const props = defineProps(['template'])
const form = useForm({
    name: props.template.value.name,
    info: props.template.value.info,
    subject: props.template.value.subject,
    html_template: props.template.value.html_template,
    text_template: props.template.value.text_template
})

const submit = () => {
    form.patch(route('notifications.update', props.template.value.id), {
        preserveScroll: false,
        onSuccess: () => {
        },
        onError: (error) => {
            /* handle the error */
        }
    })
}
const onChange = (value) => {
    // Rich text field change callback
    form.html_template = value
}
</script>

<template>
    <AppLayout title="Email Template Update">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-400">
                {{ template.name }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <FormSection @submitted="submit">
                    <template #title>
                        Update Template
                    </template>

                    <template #description>
                        Customize the messaging of the email templates sent
                        to customers for various application touch points.
                    </template>

                    <template #form>
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <Label for="name">
                                Name <small>(internal use only)</small>
                            </Label>
                            <Input id="name" type="text"
                                   class="mt-1 block w-full"
                                   v-model="form.name" ref="name" />
                            <InputError :message="form.errors.name"
                                        class="mt-2" />
                        </div>

                        <!-- Info -->
                        <div class="col-span-6 sm:col-span-4">
                            <Label for="info">
                                Info <small>(internal use only)</small>
                            </Label>
                            <Text class="mt-1 block w-full"
                                  v-model="form.info" ref="info" />
                            <InputError :message="form.errors.info"
                                        class="mt-2" />
                        </div>

                        <div
                            class="col-span-6 sm:col-span-4 my-6 border-t border-gray-200"></div>

                        <!-- Subject -->
                        <div class="col-span-6 sm:col-span-4">
                            <div
                                class="flex flex-row justify-between content-center">
                                <Label for="subject" value="Subject"
                                       :required="true" />
                                <Dropdown>
                                    <template #trigger>
                                        <svg class="w-4 h-4"
                                             xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </template>
                                    <template #content>
                                        <span
                                            class="text-sm font-semibold uppercase py-2 px-3 block border-b border-gray-200">
                                            Variables
                                        </span>
                                        <a @click="form.subject = form.subject + `{{ ${variable} }}`"
                                           v-for="variable in template.variables"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                                           role="menuitem">
                                            {{ variable }}
                                        </a>
                                    </template>
                                </Dropdown>
                            </div>
                            <Input id="subject" type="text"
                                   class="mt-1 block w-full"
                                   v-model="form.subject" ref="subject"
                                   autocomplete="subject" />
                            <InputError :message="form.errors.subject"
                                        class="mt-2" />
                        </div>

                        <!-- HTML -->
                        <div class="col-span-6 sm:col-span-4">
                            <Label for="html_template" value="Message body"
                                   :required="true" />
                            <div class="my-1">
                                <RichText v-model="form.html_template"
                                          :dynamic-variables="template.variables" />
                            </div>
                            <InputError :message="form.errors.html_template"
                                        class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful"
                                       class="mr-3">
                            Updated.
                        </ActionMessage>

                        <Link
                            :href="template.preview_url"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150 mx-3">
                            Preview
                        </Link>

                        <Button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Update
                        </Button>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
