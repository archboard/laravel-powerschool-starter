<template>
  <FormField
    v-model="localValue"
    :type="type"
    :error="error"
    :help="helpText"
    :required="required"
    :disabled="disabled"
  >
    {{ label }}
  </FormField>
</template>

<script setup>
import { fieldProps, fieldEmits } from '@/composition/useFormField.js'
import FormField from '@/components/forms/FormField.vue'
import { useVModel } from '@vueuse/core'

const props = defineProps({
  ...fieldProps,
  type: {
    type: String,
    default: 'text',
  }
})
const emit = defineEmits([
  ...fieldEmits,
])
const localValue = useVModel(props, 'modelValue', emit)
</script>
