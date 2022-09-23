import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default () => {
  const page = usePage()
  return computed(() => page.props.value.user || {})
}
