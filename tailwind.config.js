/**
 * Tailwind CSS Configuration
 * File path: tailwind.config.js
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

module.exports = {
  content: [
    "./views/**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'primary': {
          50: '#e6f0ff',
          100: '#cce0ff',
          200: '#99c2ff',
          300: '#66a3ff',
          400: '#3385ff',
          500: '#0066ff', // Primary color
          600: '#0052cc',
          700: '#003d99',
          800: '#002966',
          900: '#001433',
        },
        'secondary': {
          50: '#f3e6ff',
          100: '#e6ccff',
          200: '#cc99ff',
          300: '#b366ff',
          400: '#9933ff',
          500: '#8000ff', // Secondary color
          600: '#6600cc',
          700: '#4d0099',
          800: '#330066',
          900: '#1a0033',
        },
      },
      fontFamily: {
        sans: ['Roboto', 'Noto Sans Arabic', 'sans-serif'],
      },
    },
    container: {
      center: true,
      padding: '1rem',
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('tailwindcss-rtl'),
  ],
}
