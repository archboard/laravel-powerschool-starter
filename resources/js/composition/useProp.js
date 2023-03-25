import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default (prop) => {
  return computed(() => {
    return usePage().props[prop]
  })
}
