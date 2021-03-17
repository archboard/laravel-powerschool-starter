<template>
  <div class="max-w-xl mx-auto px-4 pt-16 pb-12 min-h-screen text-gray-800 dark:text-gray-100">
    <h1 class="font-bold text-2xl leading-5 mb-8">Laravel Inertia Starter</h1>
    <p class="text-lg mb-4">A Laravel starter with Inertia and some components to get you started.</p>

    <inertia-link :href="$route('settings.personal')">Settings</inertia-link>

    <h2 class="text-xl font-bold mb-2">Notifications</h2>
    <div class="space-y-2">
      <div>
        <app-button @click="notify('This is a success!', 'success')" color="green">Success</app-button>
      </div>
      <div>
        <app-button @click.prevent="notify('This is an error!', 'error')" color="red">Error</app-button>
      </div>
      <div>
        <app-button @click.prevent="notify('This is a warning!', 'warning')" color="yellow">Warning</app-button>
      </div>
      <div>
        <app-button @click.prevent="notify('This is neutral!')" color="neutral">Neutral</app-button>
      </div>
    </div>

    <h2 class="text-xl font-bold mb-2 mt-10">Alerts</h2>

    <div class="space-y-4">
      <Alert>My neutral alert.</Alert>
      <Alert level="success">My success alert.</Alert>
      <Alert level="warning">My warning alert.</Alert>
      <Alert level="error">My error alert.</Alert>
    </div>

    <h2 class="text-xl font-bold mb-2 mt-10">Modal</h2>

    <div class="space-y-2">
      <div>
        <app-button @click.prevent="showModal = true">Launch Modal</app-button>
      </div>
      <div>
        <confirm-button @confirm="confirmed" color="red">
          Delete?

          <template v-slot:actionText>
            I'm not scared
          </template>
        </confirm-button>
      </div>
      <div>
        <app-button @click.prevent="showDoubleModal = true" color="white">Modal + Confirm Modal</app-button>
      </div>
    </div>


    <modal
      v-if="showModal"
      @close="showModal = false"
      @action="modalAction"
      :auto-close="false"
      size="sm"
      action-text="Got it!"
      headline="Optional Headline"
    >
      <p class="text-sm text-gray-500">
        This is some default text in the modal, but you could put anything in here like forms or something else.
      </p>
    </modal>

    <modal
      v-if="showDoubleModal"
      @close="showDoubleModal = false"
      @action="modalAction"
      :auto-close="false"
      size="sm"
      action-text="Got it!"
      ref="doubleModal"
    >
      <p class="text-sm text-gray-500">
        This modal launches a confirmation modal.
      </p>

      <template v-slot:actions>
        <confirm-button @confirm="nestedConfirm" type="button" class="sm:ml-1">
          Launch Confirm Modal
        </confirm-button>
        <app-button @click.prevent="() => $refs.doubleModal.close()" type="button" color="white">
          Cancel
        </app-button>
      </template>
    </modal>

    <notifications />
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import Notifications from '@/components/Notifications'
import Alert from '@/components/Alert'
import Modal from '@/components/Modal'
import ConfirmButton from '@/components/ConfirmButton'
import authCheck from '../mixins/AuthCheck'

export default defineComponent({
  components: {
    ConfirmButton,
    Modal,
    Alert,
    Notifications,
  },

  setup () {
    authCheck()
  },

  data () {
    return {
      showModal: false,
      showDoubleModal: false,
    }
  },

  methods: {
    notify (text, level) {
      this.$notify({
        text,
        level,
      })
    },

    modalAction (closeModal) {
      console.log('Modal action.')

      closeModal()
    },

    confirmed (closeModal) {
      console.log('Action confirmed.')

      closeModal()
    },

    nestedConfirm (closeModal) {
      console.log('Nested action confirmed.')

      closeModal()
      this.$refs.doubleModal.close()
      this.$success('Double modal complete!')
    },
  }
})
</script>
