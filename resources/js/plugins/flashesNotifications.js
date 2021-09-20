import store from '@/stores/notifications'

export default (flash) => {
  if (flash) {
    Object.keys(flash).forEach(level => {
      const text = flash[level]

      if (text) {
        store.addNotification({ level, text })
      }
    })
  }
}
