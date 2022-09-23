import { computed } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default (prop) => {
  const page = usePage()

  return computed(() => {
    return page.props.value[prop]
  })
}
