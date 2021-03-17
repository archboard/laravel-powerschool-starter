<template>
  <Layout>
    <ValidationErrors class="mb-4" />

    <form @submit.prevent="submit">
      <Fieldset>
        <InputWrap :error="form.errors.email">
          <Label for="email" class="text-base">{{ __('Email') }}</Label>
          <Input id="email" type="email" class="text-lg" v-model="form.email" required autofocus autocomplete="username" />
        </InputWrap>

        <InputWrap :error="form.errors.password">
          <Label for="password" class="text-base">{{ __('Password') }}</Label>
          <Input id="password" type="password" class="text-lg" v-model="form.password" required autocomplete="new-password" />
        </InputWrap>

        <InputWrap>
          <Label for="password_confirmation" class="text-base">{{ __('Confirm Password') }}</Label>
          <Input id="password_confirmation" type="password" class="text-lg" v-model="form.password_confirmation" required autocomplete="new-password" />
        </InputWrap>
      </Fieldset>

      <div class="mt-6">
        <Button :loading="form.processing" :is-block="true" size="lg">
          {{ __('Reset Password') }}
        </Button>
      </div>
    </form>
  </Layout>
</template>

<script>
import Fieldset from '@/components/forms/Fieldset'
import InputWrap from '@/components/forms/InputWrap'
import Label from '@/components/forms/Label'
import Input from '@/components/forms/Input'
import Button from '@/components/Button'
import ValidationErrors from '@/components/ValidationErrors'
import Layout from '../../layouts/Guest'

export default {
  components: {
    Layout,
    ValidationErrors,
    Button,
    Input,
    Label,
    InputWrap,
    Fieldset,
  },

  props: {
    email: String,
    token: String,
  },

  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: '',
      })
    }
  },

  methods: {
    submit() {
      this.form.post(this.$route('password.update'), {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    }
  }
}
</script>
