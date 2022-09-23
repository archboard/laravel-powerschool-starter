<template>
  <AppCombobox
    v-model="localValue"
    v-model:query="query"
    :options="filteredOptions"
    :has-error="hasError"
    :disabled="disabled"
  />
</template>

<script setup>
import { computed, ref } from 'vue'
import AppCombobox from '@/components/forms/AppCombobox.vue'

const props = defineProps({
  modelValue: Object,
  objectId: Number,
  options: {
    type: Array,
    required: true,
  },
  hasError: {
    type: Boolean,
    default: () => false,
  },
  disabled: {
    type: Boolean,
    default: () => false,
  },
})
const emit = defineEmits([
  'update:modelValue',
  'update:objectId',
])
const localValue = computed({
  get: () => props.modelValue || {},
  set: (value) => {
    emit('update:modelValue', value)
    emit('update:objectId', value?.id)
  }
})
const query = ref()
const filteredOptions = computed(() => {
  if (!query.value || typeof query.value !== 'string') {
    return props.options
  }

  const q = query.value.toLowerCase()

  return props.options.filter(o => o.name.toLowerCase().includes(q))
})
</script>
