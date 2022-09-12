<template>
  <CheckboxWrapper>
    <Checkbox v-model:checked="localValue" />
    <CheckboxText>
      <slot /> <Req v-if="required" />
    </CheckboxText>
  </CheckboxWrapper>
</template>

<script setup>
import { computed } from 'vue'
import CheckboxWrapper from './CheckboxWrapper.vue'
import Checkbox from './Checkbox.vue'
import CheckboxText from './CheckboxText.vue'
import Req from '@/components/forms/Req.vue'

const props = defineProps({
  modelValue: Boolean,
  required: {
    type: Boolean,
    default: () => false,
  }
})
const emit = defineEmits(['update:modelValue'])
const localValue = computed({
  get: () => props.modelValue,
  set: value => emit('update:modelValue', value)
})
</script>
