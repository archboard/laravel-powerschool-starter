<template>
  <Layout>
    <form @submit.prevent="submit">
      <Fieldset>
        <InputWrap :error="form.errors.email">
          <Label for="email" class="text-base">{{ __('Email') }}</Label>
          <Input id="email" type="email" class="text-lg" v-model="form.email" required autofocus autocomplete="username" />
        </InputWrap>

        <InputWrap>
          <Label for="password" class="text-base">{{ __('Password') }}</Label>
          <Input id="password" type="password" class="text-lg" v-model="form.password" required autocomplete="current-password" />
        </InputWrap>

        <InputWrap>
          <label class="flex items-center">
            <Checkbox name="remember" v-model:checked="form.remember" />
            <CheckboxText>{{ __('Remember me') }}</CheckboxText>
          </label>
        </InputWrap>
      </Fieldset>

      <div class="my-4">
        <Button :loading="form.processing" :is-block="true" size="lg">
          {{ __('Log in') }}
        </Button>
      </div>

      <p>
        <InertiaLink v-if="canResetPassword" :href="$route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 transition">
          {{ __('Forgot your password ?') }}
        </InertiaLink>
      </p>
    </form>
  </Layout>
</template>

<script>
import Layout from '@/layouts/Guest'
import Fieldset from '@/components/forms/Fieldset'
import InputWrap from '@/components/forms/InputWrap'
import Label from '@/components/forms/Label'
import Input from '@/components/forms/Input'
import Checkbox from '@/components/forms/Checkbox'
import Button from '@/components/Button'
import ValidationErrors from '@/components/ValidationErrors'
import Alert from '../../components/Alert'
import CheckboxText from '../../components/forms/CheckboxText'

export default {
  components: {
    CheckboxText,
    Alert,
    Layout,
    ValidationErrors,
    Button,
    Checkbox,
    Input,
    Label,
    InputWrap,
    Fieldset,
  },

  props: {
    canResetPassword: Boolean,
    status: String
  },

  data() {
    return {
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false
      })
    }
  },

  methods: {
    submit() {
      this.form
        .transform(data => ({
          ...data,
          remember: this.form.remember ? 'on' : ''
        }))
        .post(this.$route('login'), {
          onFinish: () => this.form.reset('password'),
        })
    }
  }
}
</script>
