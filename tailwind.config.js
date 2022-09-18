const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  content: [
    'resources/js/**/*.{vue,js}',
    'resources/views/**/*.blade.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: [
          'Inter',
          ...defaultTheme.fontFamily.sans,
        ]
      },
      colors: {
        transparent: 'transparent',
        current: 'currentColor',
        // hard code from the actual color set you want
        primary: {
          50: '#fdf4ff',
          100: '#fae8ff',
          200: '#f5d0fe',
          300: '#f0abfc',
          400: '#e879f9',
          500: '#d946ef',
          600: '#c026d3',
          700: '#a21caf',
          800: '#86198f',
          900: '#701a75',
        },
        gray: {
          ...colors.zinc,
        }
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
