import { computed } from 'vue'

const bgColors = {
  success: 'bg-green-50',
  neutral: 'bg-gray-50',
  error: 'bg-red-50',
  warning: 'bg-yellow-50',
}
const iconColors = {
  success: 'text-green-400',
  neutral: 'text-gray-400',
  error: 'text-red-400',
  warning: 'text-yellow-400',
}
const textColors = {
  success: 'text-green-800',
  neutral: 'text-gray-800',
  error: 'text-red-800',
  warning: 'text-yellow-800',
}
const dismissColors = {
  success: 'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-offset-green-50 focus:ring-green-600',
  neutral: 'bg-gray-50 text-gray-500 hover:bg-gray-100 focus:ring-offset-gray-50 focus:ring-gray-600',
  error: 'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-offset-red-50 focus:ring-red-600',
  warning: 'bg-yellow-50 text-yellow-500 hover:bg-yellow-100 focus:ring-offset-yellow-50 focus:ring-yellow-600',
}

export default (level) => {
  return {
    iconColor: computed(() => iconColors[level] || iconColors.neutral),
    bgColor: computed(() => bgColors[level] || bgColors.neutral),
    textColor: computed(() => textColors[level] || textColors.neutral),
    dismissColor: computed(() => dismissColors[level] || dismissColors.neutral),
  }
}
