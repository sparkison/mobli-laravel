import {computed, ref, unref} from 'vue'
import moment from 'moment'

export default function (initialParam) {
    const reuseData = computed(() => unref(initialParam))

    const selected = ref([])
    const badgeWrapperClass = ref('break-all flex flex-wrap -ml-1 ')
    const badgeClass = ref('inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-600 text-white m-1')

    const allSelected = computed(() => {
        const value = reuseData.value || null
        if (value) {
            return selected.value.length === value.perPage
                || selected.value.length === value.total
        }
        return null
    })

    const getModelValue = (model, key, defaultValue = '') => {
        if (model.hasOwnProperty(key)) {
            const value = model[key]
            if (key.includes('_at')) {
                // Parse UTC and pass back in local time if column contains `_at`, indicating date
                return moment.utc(value, 'YYYY-MM-DD HH:mm:ss').local().format('lll')
            }
            return value
        } else if (key.includes(".")) {
            let values = getDescendantProp(model, key) || []
            if (values) {
                values = Array.isArray(values)
                    ? values.map(v => `<span class="${badgeClass.value}">${v}</span>`)
                    : [`<span className="${badgeClass.value}">${values}</span>`]
                return `<div class="${badgeWrapperClass.value}">` + values.join(" ") + "</div>"
            }
        }
        return defaultValue
    }
    const getDescendantProp = (obj, path) => {
        return path.split('.').reduce((acc, part) => {
            if (Array.isArray(acc)) {
                return acc.map(item => item && item[part])
            }
            return acc && acc[part]
        }, obj)
    }
    const toggleAll = () => {
        if (allSelected.value) {
            selected.value = []
        } else {
            selected.value = reuseData.value.data.map(m => {
                return { ...m }
            })
        }
    }
    const toggleRow = (model) => {
        if (selected.value.findIndex(m => m.id === model.id) !== -1) {
            selected.value = selected.value.filter(m => m.id !== model.id)
        } else {
            selected.value.push(model)
        }
    }
    const isRowSelected = (model) => {
        return selected.value.findIndex(m => m.id === model.id) !== -1
    }
    return {
        allSelected,
        selected,
        getModelValue,
        toggleAll,
        toggleRow,
        isRowSelected
    }
}
