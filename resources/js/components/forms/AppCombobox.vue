<template>
  <Combobox v-model="localValue">
    <div
      class="relative w-full cursor-default"
    >
      <ComboboxInput
        class="shadow-sm block w-full rounded-lg text-sm focus:outline-none focus:ring-2"
        :class="{
          'focus:ring-brand-blue focus:border-brand-blue border-gray-300': !hasError,
          'pr-10 border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': hasError,
        }"
        @change="$emit('update:query', $event.target.value)"
        :display-value="handleDisplay"
      />

      <ComboboxButton class="absolute inset-y-0 right-0 flex items-center pr-2">
        <ChevronUpDownIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
      </ComboboxButton>
    </div>
    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-out"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <ComboboxOptions
        class="absolute z-10 mt-1 top-10 max-h-60 w-full min-w-[16rem] overflow-auto rounded-md bg-white text-base ring-opacity-5 focus:outline-none sm:text-sm"
        :class="{
          'py-1 shadow-lg ring-1 ring-black': !!query
        }"
      >
        <div
          v-if="options.length === 0 && query !== ''"
          class="relative cursor-default select-none py-2 px-4 text-gray-700"
        >
          Nothing found.
        </div>

        <ComboboxOption
          as="template"
          v-slot="{ active, selected }"
          v-for="item in options"
          :key="item.id"
          :value="item"
        >
          <li
            class="relative cursor-default select-none py-2 pl-10 pr-4"
            :class="{
              'bg-brand-purple text-white': active,
              'text-black': !active,
            }"
          >
            <span
              class="block truncate"
              :class="{ 'font-medium': selected, 'font-normal': !selected }"
            >
              <slot name="item" :selected="selected" :active="active" :item="item">
                {{ item.name || item.title }}
              </slot>
            </span>
            <span
              v-if="selected"
              class="absolute inset-y-0 left-0 flex items-center pl-3"
              :class="{ 'text-white': active, 'text-black': !active }"
            >
              <CheckIcon class="h-5 w-5" aria-hidden="true" />
            </span>
          </li>
        </ComboboxOption>
      </ComboboxOptions>
    </transition>
  </Combobox>
</template>

<script setup>
import {
  Combobox,
  ComboboxInput,
  ComboboxOptions,
  ComboboxOption,
  ComboboxButton,
} from '@headlessui/vue'
import { useVModel } from '@vueuse/core'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/24/solid'
import AppInput from '@/components/forms/AppInput.vue'

const props = defineProps({
  modelValue: [Number, String, Object],
  query: {
    type: String,
  },
  options: {
    type: Array,
    required: true,
  },
  displayValue: Function,
  hasError: {
    type: Boolean,
    default: () => false,
  }
})
const emit = defineEmits(['update:modelValue', 'update:query'])
const localValue = useVModel(props, 'modelValue', emit)

const handleDisplay = item => {
  if (typeof props.displayValue === 'function') {
    return props.displayValue(item) || ''
  }

  return item?.name || item?.title || ''
}
</script>
