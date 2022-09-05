import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
const path = require('path')

export default ({ mode }) => {
  process.env = {
    ...process.env,
    ...loadEnv(mode, process.cwd(), 'APP_'),
  }
  const domain = (new URL(process.env.APP_URL)).hostname

  return defineConfig({
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
    server: {
      https: {
        key: process.env.APP_SSL_KEY,
        cert: process.env.APP_SSL_CERT,
      },
      domain,
      hmr: {
        host: domain,
      }
    }
  })
}
