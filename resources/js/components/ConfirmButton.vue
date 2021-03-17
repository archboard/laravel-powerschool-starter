<template>
  <div class="inline-block">
    <app-button v-bind="$attrs" @click.prevent="show = true">
      <slot />
    </app-button>

    <modal
      v-if="show"
      @close="show = false"
      ref="modal"
    >
      <div>
        <slot name="icon">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
        </slot>

        <div class="mt-3 text-center sm:mt-5 w-full">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-headline">
            <slot name="headline">
              Are you sure?
            </slot>
          </h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500 dark:text-gray-300">
              <slot name="content">
                This action is destructive and cannot be undone.
              </slot>
            </p>
          </div>
        </div>
      </div>

      <template v-slot:actions>
        <div class="space-y-2 sm:space-y-0 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense w-full">
          <app-button type="button" @click.prevent="confirmed" class="w-full">
            <slot name="actionText">
              Confirm
            </slot>
          </app-button>
          <app-button type="button" color="white" @click.prevent="closeModal" class="w-full">
            Cancel
          </app-button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import Modal from '@/components/Modal'

export default defineComponent({
  components: {
    Modal,
  },

  props: {},
  emits: ['confirm'],

  data () {
    return {
      show: false,
    }
  },

  methods: {
    confirmed () {
      this.$emit('confirm', () => {
        this.closeModal()
      })
    },

    closeModal () {
      this.$refs.modal.close()
    }
  }
})
</script>
