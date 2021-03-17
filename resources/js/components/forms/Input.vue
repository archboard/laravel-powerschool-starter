<template>
  <input
    :type="type"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    class="shadow-sm focus:ring-2 focus:ring-primary-500 focus:ring-offset-primary-500 focus:border-primary-500 block w-full border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-900 dark:focus:border-primary-500 transition duration-150 ease-in-out"
  >
</template>

<script>
export default {
  props: {
    modelValue: {
      type: [String, Number],
    },
    type: {
      type: String,
      default: 'text',
    }
  },
  emits: ['update:modelValue'],
}
</script>
