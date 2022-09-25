export const fieldProps = {
  modelValue: [Object, Boolean, String, Number],
  label: String,
  type: String,
  options: Array,
  error: String,
  disabled: Boolean,
  required: {
    type: Boolean,
    default: () => false,
  },
  helpText: {
    type: String,
    default: '',
  },
}

export const fieldEmits = ['update:modelValue']