<template>
  <input
    type="checkbox"
    :value="value"
    v-model="proxyChecked"
    @change="$emit('change', proxyChecked)"
   class="cursor-pointer text-primary-500 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded"
  >
</template>

<script>
export default {
  emits: [
    'update:checked',
    'change'
  ],

  props: {
    checked: {
      type: [Array, Boolean],
      default: false,
    },
    value: {
      default: null,
    },
  },

  computed: {
    proxyChecked: {
      get () {
        return this.checked;
      },

      set (val) {
        this.$emit("update:checked", val);
      },
    },
  },
}
</script>
