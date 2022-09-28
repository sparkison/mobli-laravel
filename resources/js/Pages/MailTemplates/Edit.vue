<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-400">
                {{ template.name }}
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="submit">
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
                            <jet-label for="name">
                                Name <small>(internal use only)</small>
                            </jet-label>
                            <jet-input id="name" type="text"
                                       class="mt-1 block w-full"
                                       v-model="form.name" ref="name" />
                            <jet-input-error :message="form.errors.name"
                                             class="mt-2" />
                        </div>

                        <!-- Info -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="info">
                                Info <small>(internal use only)</small>
                            </jet-label>
                            <jet-text class="mt-1 block w-full"
                                      v-model="form.info" ref="info" />
                            <jet-input-error :message="form.errors.info"
                                             class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4 my-6 border-t border-gray-200"></div>

                        <!-- Subject -->
                        <div class="col-span-6 sm:col-span-4">
                            <div class="flex flex-row justify-between content-center">
                                <jet-label for="subject" value="Subject" :required="true" />
                                <jet-dropdown>
                                    <template #trigger>
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </template>
                                    <template #content>
                                        <span class="text-sm font-semibold uppercase py-2 px-3 block border-b border-gray-200">
                                            Variables
                                        </span>
                                        <a @click="form.subject = form.subject + `{{ ${variable} }}`"
                                           v-for="variable in template.variables"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900" role="menuitem">
                                            {{ variable }}
                                        </a>
                                    </template>
                                </jet-dropdown>
                            </div>
                            <jet-input id="subject" type="text"
                                       class="mt-1 block w-full"
                                       v-model="form.subject" ref="subject"
                                       autocomplete="subject" />
                            <jet-input-error :message="form.errors.subject"
                                             class="mt-2" />
                        </div>

                        <!-- HTML -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="html_template" value="Message body" :required="true" />
                            <div class="my-1">
                                <rich-text v-model="form.html_template"
                                           :dynamic-variables="template.variables" />
                            </div>
                            <jet-input-error :message="form.errors.html_template"
                                             class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful"
                                            class="mr-3">
                            Updated.
                        </jet-action-message>

                        <inertia-link
                            :href="template.preview_url"
                            class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150 mx-3">
                            Preview
                        </inertia-link>

                        <jet-button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Update
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import JetActionMessage from '@/Jetstream/ActionMessage'
import JetButton from '@/Jetstream/Button'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetFormSection from '@/Jetstream/FormSection'
import JetInput from '@/Jetstream/Input'
import JetText from '@/Jetstream/Text'
import RichText from '@/Jetstream/RichText'
import JetInputError from '@/Jetstream/InputError'
import JetLabel from '@/Jetstream/Label'
import JetDropdown from '@/Jetstream/Dropdown'

export default {
    props: ['template'],

    components: {
        AppLayout,
        JetActionMessage,
        JetButton,
        JetSecondaryButton,
        JetFormSection,
        JetInput,
        JetText,
        RichText,
        JetInputError,
        JetLabel,
        JetDropdown
    },

    data () {
        return {
            form: this.$inertia.form({
                name: this.template.name,
                info: this.template.info,
                subject: this.template.subject,
                html_template: this.template.html_template,
                text_template: this.template.text_template
            }),
        }
    },

    methods: {
        submit () {
            this.form.patch(route('notifications.update', this.template.id), {
                preserveScroll: false,
                onSuccess: () => {
                },
                onError: (error) => { this.$toast().error(Object.values(error).join(" ")) }
            })
        },
        onChange (value) {
            // Rich text field change callback
            this.form.html_template = value
        }
    }
}
</script>
