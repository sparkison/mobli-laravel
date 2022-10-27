<script setup>
import {useForm} from '@inertiajs/inertia-vue3'
import FormSection from '@/Jetstream/FormSection'
import ActionMessage from '@/Jetstream/ActionMessage'
import DangerButton from '@/Jetstream/DangerButton'
import Button from '@/Jetstream/Button'
import SecondaryButton from '@/Jetstream/SecondaryButton'
import Label from '@/Jetstream/Label'
import Input from '@/Jetstream/Input'
import InputError from '@/Jetstream/InputError'
import Toggle from '@/Jetstream/ToggleSwitch'
import ConfirmationModal from '@/Jetstream/ConfirmationModal'
import {onMounted, ref} from 'vue'
import {useToast} from 'vue-toastification'

const props = defineProps(['type', 'title', 'description', 'model']);
const emit = defineEmits(['success']);
const isReadyToSubmit = ref(true)
const confirmingDeletion = ref(false)

/*
 * Route names and confirmation text
 */
const updateRouteName = 'user.update';
const storeRouteName = 'user.store';
const destroyRouteName = 'user.destroy';
const archiveAlertTitle = 'Are you sure you want to archive this user?';
const destroyAlertTitle = 'Are you sure you want to delete this user? This action cannot be undone.';

/*
 * Create the forms
 */
const form = useForm({
    name: '',
    email: '',
    is_admin: false,
})

onMounted(() => {
    const data = form.data()
    if (props.model) {
        for (let key of Object.keys(data)) {
            if (props.model.hasOwnProperty(key)) {
                form[key] = props.model[key]
            }
        }
    }
})

/*
 * Form actions
 */
const submit = () => {
    const method = props.type === 'POST' ? 'post' : 'patch'
    const url = props.type === 'POST'
        ? route(storeRouteName)
        : route(updateRouteName, props.model.id)

    form[method](url, {
        preserveScroll: false,
        onSuccess: () => { emit('success') },
        onError: (error) => { useToast().error(Object.values(error).join(" ")) }
    })
};
const destroy = () => {
    form.delete(route(destroyRouteName, props.model.id), {
        preserveScroll: false,
        onSuccess: () => {
            // Hide delete modal and emit success function
            confirmingDeletion.value = false
            emit('success')
        },
        onError: (error) => { useToast().error(Object.values(error).join(" ")) }
    })
};
const restore = () => {
    /*
     * Not being used
     */
    return null
};

</script>

<template>
    <div>
        <FormSection @submitted="submit">
            <template #title>
                {{ title }}
            </template>

            <template #description>
                {{ description }}
            </template>

            <template #form>
                <template v-if="$page.props.isAdmin">
                    <!-- Admin -->
                    <div class="col-span-6">
                        <Label for="admin" value="Admin" />
                        <Toggle v-model="form.is_admin" :value="form.is_admin" />
                        <div class="mt-2 text-xs text-gray-600">
                            Admins have full access to manage app data, users, etc. Use with caution.
                        </div>
                    </div>
                </template>

                <!-- Profile -->
                <div class="col-span-6">
                    <Label for="name" value="Full name" :required="true" />
                    <Input id="name" type="text"
                           class="mt-1 block w-full"
                           v-model="form.name" ref="name"
                           autocomplete="name" />
                    <InputError :message="form.errors.name"
                                class="mt-2" />
                </div>
                <div class="col-span-6">
                    <Label for="email" value="Email" :required="true" />
                    <Input id="email" type="email"
                           class="mt-1 block w-full"
                           v-model="form.email" ref="email"
                           autocomplete="email" />
                    <InputError :message="form.errors.email"
                                class="mt-2" />
                </div>
            </template>

            <template #actions>
                <DangerButton
                    v-if="type !== 'POST'"
                    type="button"
                    @click.native="confirmingDeletion = true"
                    class="mr-3"
                    :class="{ 'opacity-25': form.processing || !isReadyToSubmit }"
                    :disabled="form.processing || !isReadyToSubmit">
                    {{ model.deleted_at === null ? 'Archive' : 'Delete' }}
                </DangerButton>
                <SecondaryButton
                    v-if="type !== 'POST' && model.deleted_at"
                    type="button"
                    @click.native="restore"
                    class="mr-3"
                    :class="{ 'opacity-25': form.processing || !isReadyToSubmit }"
                    :disabled="form.processing || !isReadyToSubmit">
                    Restore
                </SecondaryButton>
                <ActionMessage :on="form.recentlySuccessful"
                               class="ml-auto mr-3">
                    {{ type === 'POST' ? 'Created.' : 'Updated.'  }}
                </ActionMessage>
                <Button
                    :class="{ 'opacity-25': form.processing || !isReadyToSubmit }"
                    :disabled="form.processing || !isReadyToSubmit">
                    {{ type === 'POST' ? 'Create' : 'Update' }}
                </Button>
            </template>
        </FormSection>

        <ConfirmationModal
            :show="confirmingDeletion"
            @close="confirmingDeletion = false">
            <template #title>
                {{ model.deleted_at === null ? 'Archive' : 'Delete' }}
            </template>
            <template #content>
                <template v-if="model.deleted_at === null">
                    {{ archiveAlertTitle }}
                </template>
                <template v-else>
                    {{ destroyAlertTitle }}
                </template>
            </template>
            <template #footer>
                <SecondaryButton @click.native="confirmingDeletion = false">
                    Nevermind
                </SecondaryButton>
                <DangerButton class="ml-2" @click.native="destroy"
                              :class="{ 'opacity-25': form.processing }"
                              :disabled="form.processing">
                    {{ model.deleted_at === null ? 'Archive' : 'Delete' }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
