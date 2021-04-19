const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  mode: 'jit',
  purge: [
    'resources/js/**/*.vue',
    'resources/views/**/*.blade.php',
  ],
  darkMode: 'class', // or 'media' or 'class'
  theme: {
    extend: {
      fontFamily: {
        sans: [
          'Inter',
          ...defaultTheme.fontFamily.sans,
        ]
      }
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      primary: {}, // hard code from the actual color set you want
      black: colors.black,
      white: colors.white,
      gray: colors.trueGray,
      red: colors.red,
      yellow: colors.yellow,
      blue: colors.blue,
      green: colors.green,
      orange: colors.orange,
    }
  },
  variants: {
    extend: {
      cursor: ['disabled'],
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/ui'),
  ],
}
