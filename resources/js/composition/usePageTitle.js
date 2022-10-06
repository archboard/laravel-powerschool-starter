import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default () => {
  const page = usePage()
  const title = computed(() => page.props.value.title)

  onMounted(() => {
    document.title = title.value
      ? `${title.value} | App Name`
      : 'App Name'
  })

  return title
}
