<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <FadeInGroup>
      <template v-for="field in fields" :key="field.key">
        <div
          :class="{
            'md:col-span-2': !field.half,
          }"
        >
          <component
            :is="FormFields[field.component]"
            v-model="localForm[field.key]"
            :error="errors[errorPrefix + field.key]"
            :options="field.options"
            :label="field.label"
            :required="field.required"
            :disabled="field.disabled"
            :help="field.help"
          />
        </div>
      </template>
    </FadeInGroup>
  </div>
</template>

<script setup>
import * as FormFields from '@/components/forms/fields'
import { useVModel } from '@vueuse/core'
import FadeInGroup from '@/components/transitions/FadeInGroup.vue'

const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
  },
  fields: {
    type: Array,
    required: true,
  },
  errors: {
    type: Object,
    required: true,
  },
  errorPrefix: {
    type: String,
    default: '',
  },
})
const emit = defineEmits(['update:modelValue'])
const localForm = useVModel(props, 'modelValue', emit)
// const localForm = computed({})
</script>
