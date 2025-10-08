export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        poppins: ['Poppins', 'sans-serif'],
      },
      colors: {
        primary: {
          50: '#fef0e6',
          100: '#fde1ce',
          200: '#fbb384',
          300: '#f8863a',
          400: '#de5d08',
          500: '#ac4806',
          600: '#f88437', // main
          700: '#7b3404',
          800: '#592503',
          900: '#3b1902',
        },
      },
    },
  },
  plugins: [],
}
