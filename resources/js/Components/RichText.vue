<script setup>
import Editor from '@tinymce/tinymce-vue'
import {onMounted, ref, watch} from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false
    },
    dynamicVariables: {
        default: null
    }
})

const emit = defineEmits(['update:modelValue'])

const show = ref(false)
const config = ref({
    height: 350,
    plugins: 'emoticons',
    menubar: 'file edit view format tools',
    toolbar: 'undo redo | styleselect | bold italic emoticons | alignleft aligncenter alignright alignjustify | outdent indent',
    content_style: '@import url(\'https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap\'); body { font-family: Nunito; }',
})
const value = ref(props.modelValue.value)

onMounted(() => {
    if (props.dynamicVariables.value) {
        initDynamicMenu()
    }
})

watch(props.modelValue, (newVal) => {
    value.value = newVal
})
watch(value, (newVal) => {
    emit('update:modelValue', newVal)
})

const initDynamicMenu = () => {
    /*
     * Dynamic variables
     * Use array of strings, will be wrapped in mustache brackets
     */
    const vars = props.dynamicVariables.value
    if (Array.isArray(vars)) {
        config.value = {
            ...config.value,
            menu: {
                custom: {
                    title: 'Dynamic Variables',
                    items: vars.join(" ")
                }
            },
            menubar: 'file edit view format tools custom',
            setup: (editor) => {
                for (let variable of vars) {
                    editor.ui.registry.addMenuItem(variable, {
                        text: variable,
                        onAction: () => {
                            editor.insertContent(' {{' + variable + '}} ')
                        }
                    })
                }

            }
        }
    }
}
</script>
<template>
    <div v-if="show">
        <Editor
            api-key=""
            plugins="code"
            :initial-value="modelValue"
            :init="config"
            v-model="value"
            :disabled="disabled"
            ref="tiny"
        />
    </div>
</template>
