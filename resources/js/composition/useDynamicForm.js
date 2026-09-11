import { useForm } from '@inertiajs/vue3'

export default (fields) => {
  const form = useForm(
    fields.reduce((carry) => {
      return carry
    }, {})
  )

  return {
    form,
  }
}
