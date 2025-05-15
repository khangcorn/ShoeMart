/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: 'class', // 👉 Thêm dòng này để bật dark mode theo class
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
