<template>
  <DatePicker
    v-model="localValue"
    :model-config="dateModelConfig"
    :popover="{ visibility: 'focus' }"
    v-bind="$attrs"
  >
    <template v-slot="{ inputValue, inputEvents }">
      <AppInput :value="inputValue" v-on="inputEvents" />
    </template>
  </DatePicker>
</template>

<script>
import { defineComponent } from 'vue'
import ModelValue from '@/mixins/ModelValue'
import { DatePicker } from 'v-calendar'
import 'v-calendar/dist/style.css'
import AppInput from '@/components/forms/AppInput.vue'

export default defineComponent({
  mixins: [ModelValue],
  components: {
    AppInput,
    DatePicker,
  },

  setup () {
    const dateModelConfig = {
      type: 'string',
      mask: 'YYYY-MM-DD',
    }

    return {
      dateModelConfig,
    }
  }
})
</script>
