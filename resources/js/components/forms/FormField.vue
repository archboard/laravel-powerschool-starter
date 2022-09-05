<template>
  <div class="space-y-1">
    <AppLabel :for="id" :has-error="!!error">
      <slot /> <Req v-if="required" />
    </AppLabel>

    <div class="relative">
      <slot name="component" :has-error="!!error" :local-value="localValue" :id="id">
        <component
          :is="component"
          v-model="localValue"
          :has-error="!!error"
          :id="id"
          :disabled="disabled"
          v-bind="{
            ...(['AppInput'].includes(component) ? { type, placeholder } : {}),
            ...(['AppSelect'].includes(component) ? { options } : {}),
          }"
        />
      </slot>

      <div v-if="error" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <ExclamationCircleIcon class="h-5 w-5 text-red-500" aria-hidden="true" />
      </div>
    </div>
    <FieldError v-if="error">
      {{ error }}
    </FieldError>

    <slot name="after">
      <HelpText v-if="help">{{ help }}</HelpText>
    </slot>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid'
import { nanoid } from 'nanoid'
import ModelValue from '@/mixins/ModelValue'
import FieldError from './FieldError.vue'
import Req from './Req.vue'
import HelpText from '@/components/forms/HelpText.vue'
import AppDatepicker from '@/components/forms/AppDatepicker.vue'

export default defineComponent({
  mixins: [ModelValue],
  components: {
    AppDatepicker,
    HelpText,
    Req,
    FieldError,
    ExclamationCircleIcon,
  },

  props: {
    component: {
      type: String,
      default: 'AppInput',
    },
    id: {
      type: String,
      default: () => nanoid(),
    },
    type: {
      type: String,
      default: 'text',
    },
    error: String,
    placeholder: {
      type: [String, Number],
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: () => false,
    },
    help: {
      type: String,
      default: null,
    },
    options: {
      type: Array,
      default: () => [],
    }
  }
})
</script>
