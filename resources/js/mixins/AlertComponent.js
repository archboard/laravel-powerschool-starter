const icons = {
  success: 'checkmark-circle',
  neutral: 'information-circle',
  warning: 'alert-circle',
  error: 'alert-circle',
}

export default {
  props: {
    level: {
      type: String,
      default: 'neutral',
    }
  },

  computed: {
    icon () {
      return icons[this.level] || icons.neutral
    }
  }
}
