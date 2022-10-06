<template>
  <Modal
    @close="$emit('close')"
    @action="save"
    :action-loading="form.processing"
    :auto-close="false"
    :headline="__('Has your timezone changed?')"
  >
    <p class="text-sm mb-4">{{ __("Your current timezone doesn't match the one in your settings. Update your timezone if it has changed.") }}</p>

    <FormField :error="form.errors.timezone">
      {{ __('Timezone') }}
      <template #component="{ id, hasError }">
        <AppCombobox
          v-model="selectedTimezone"
          v-model:query="query"
          :options="filteredTimezones"
          :has-error="hasError"
          :id="id"
        />
      </template>
    </FormField>
  </Modal>
</template>

<script setup>
import Modal from '@/components/modals/Modal.vue'
import FormField from '@/components/forms/FormField.vue'
import useDates from '@/composition/useDates.js'
import AppCombobox from '@/components/forms/AppCombobox.vue'
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({})
const emit = defineEmits(['close'])

const { dayjs, fetchTimezones } = useDates()
const timezones = ref([])
const form = useForm({
  timezone: dayjs.tz.guess(),
})
const save = (close) => {
  form.timezone = selectedTimezone.value?.value

  form.put('/timezone', {
    preserveScroll: true,
    onSuccess () {
      close()
    }
  })
}

fetchTimezones().then(zones => {
  timezones.value = zones
  selectedTimezone.value = zones.find(z => z.value === dayjs.tz.guess()) || null
})
const query = ref()
const selectedTimezone = ref()
const filteredTimezones = computed(() => {
  if (!query.value) {
    return timezones.value
  }

  const q = query.value.toLowerCase()
  return timezones.value.filter(z => z.label.toLowerCase().includes(q))
})
</script>
