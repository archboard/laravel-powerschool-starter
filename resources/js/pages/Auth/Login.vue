<template>
  <Layout>
    <form @submit.prevent="submit">
      <AppFieldset>
        <FormField type="email" :error="form.errors.email">
          {{ __('Email') }}
        </FormField>

        <FormField type="password" :error="form.errors.password">
          {{ __('Password') }}
        </FormField>

        <FormField>
          <template #component>
            <AppCheckbox v-model="form.remember">
              {{ __('Remember me') }}
            </AppCheckbox>
          </template>
        </FormField>
      </AppFieldset>

      <div class="my-4">
        <AppButton :loading="form.processing" size="lg">
          {{ __('Log in') }}
        </AppButton>
      </div>

      <p>
        <InertiaLink v-if="canResetPassword" :href="$route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 transition">
          {{ __('Forgot your password ?') }}
        </InertiaLink>
      </p>
    </form>
  </Layout>
</template>

<script setup>
import Layout from '@/layouts/Guest.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import AppFieldset from '@/components/forms/AppFieldset.vue'
import FormField from '@/components/forms/FormField.vue'
import AppCheckbox from '@/components/forms/AppCheckbox.vue'
import AppButton from '@/components/AppButton.vue'

const props = defineProps({
  canResetPassword: Boolean,
  status: String
})
const form = useForm({
  email: '',
  password: '',
  remember: false
})
const submit = () => {
  form.post('/login', {
    onError: () => {
      form.reset('password')
    }
  })
}
</script>
