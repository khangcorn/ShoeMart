/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  variants: {
    extend: {
      opacity: ['group-hover'],
      scale: ['group-hover'],
      visibility: ['group-hover'],
    },
  },
  plugins: [],
};
