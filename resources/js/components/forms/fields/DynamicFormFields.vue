<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <FadeInGroup>
      <template v-for="field in fields" :key="field.attribute">
        <div
          v-if="!simplified || (simplified && !field.hide_for_simple)"
          :class="{
            'md:col-span-2': !field.half,
          }"
        >
          <component
            :is="FormFields[field.component]"
            v-model="localForm[field.attribute]"
            :error="errors[errorPrefix + field.attribute]"
            :options="field.options"
            :label="field.label"
            :required="field.rules.includes('required')"
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
  simplified: {
    type: Boolean,
  }
})
const emit = defineEmits(['update:modelValue'])
const localForm = useVModel(props, 'modelValue', emit)
</script>
