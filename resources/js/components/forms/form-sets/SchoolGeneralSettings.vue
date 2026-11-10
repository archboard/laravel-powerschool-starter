<template>
  <form @submit.prevent="save">
    <SplitForm :loading="form.processing">
      <template #headline>
        <Headline3>{{ __('General settings') }}</Headline3>
      </template>

      <FormField :error="form.errors.timezone" :help="__('When a user has not set their timezone, it will use this as their timezone.')" required class="col-span-6 sm:col-span-3">
        {{ __("School's default timezone") }}
        <template #component="{ id, hasError }">
          <TimezoneCombobox v-model="form.timezone" :id="id" :has-error="hasError" />
        </template>
      </FormField>
    </SplitForm>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import SplitForm from '@/components/SplitForm.vue'
import Headline3 from '@/components/Headline3.vue'
import FormField from '@/components/forms/FormField.vue'
import TimezoneCombobox from '@/components/forms/TimezoneCombobox.vue'

const props = defineProps({
  school: Object,
})
const form = useForm({
  timezone: props.school.timezone,
})
const save = () => form.put('/settings/school', { preserveScroll: true })
</script>
