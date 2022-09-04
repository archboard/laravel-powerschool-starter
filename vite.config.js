import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
const path = require('path')

export default defineConfig({
  resolve: {
    alias: {
      axios: path.resolve('node_modules/axios/dist/axios.js'),
    },
  },
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
      ],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      }
    })
  ],
})
