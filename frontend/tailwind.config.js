/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {
      colors: {
        brand: {
          orange: '#E8702A',
          blue:   '#4A90D9',
        },
      },
    },
  },
  plugins: [],
}
