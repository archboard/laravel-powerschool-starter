import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default () => {
  const page = usePage()
  const title = computed(() => page.props.title)

  onMounted(() => {
    document.title = title.value
      ? `${title.value} | ${import.meta.env.APP_NAME}`
      : import.meta.env.APP_NAME
  })

  return title
}
