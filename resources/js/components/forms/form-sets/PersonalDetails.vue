<template>
  <form @submit.prevent="submit" data-cy="personal-details">
    <SplitForm cancel="/settings/personal" :loading="form.processing">
      <template #headline>
        <Headline3>{{ __('Personal details') }}</Headline3>
        <HelpText>{{ __(`These items are synchronized from your SIS, so they may be overwritten on the next sync or log in.`) }}</HelpText>
      </template>

      <FormField v-model="form.first_name" :error="form.errors.first_name" class="col-span-6 sm:col-span-3">
        {{ __('First name') }}
      </FormField>
      <FormField v-model="form.last_name" :error="form.errors.last_name" class="col-span-6 sm:col-span-3">
        {{ __('Last name') }}
      </FormField>
      <FormField v-model="form.email" :error="form.errors.email" type="email" class="col-span-6">
        {{ __('Email') }}
      </FormField>
    </SplitForm>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import pick from 'just-pick'
import SplitForm from '@/components/SplitForm.vue'
import Headline3 from '@/components/Headline3.vue'
import HelpText from '@/components/forms/HelpText.vue'
import FormField from '@/components/forms/FormField.vue'
import useProp from '@/composition/useProp.js'

const user = useProp('user')
const form = useForm({
  ...pick(user.value, 'first_name', 'last_name', 'email'),
})
const submit = () => {
  form.post('/settings/personal', {
    preserveScroll: true,
  })
}
</script>
