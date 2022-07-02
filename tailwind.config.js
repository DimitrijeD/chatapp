const colors = require('tailwindcss/colors')

module.exports = {
  purge: [],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
          orange: colors.orange,
          lime: colors.lime,
          emerald: colors.emerald,
          teal: colors.teal,
          cyan: colors.cyan,
          sky: colors.sky,
          blueGray: colors.blueGray,
          coolGray: colors.coolGray,
          trueGray: colors.trueGray,
          warmGray: colors.warmGray,
          amber: colors.amber,
          indigo: colors.indigo,
          violet: colors.violet,
          purple: colors.purple,
          rose: colors.rose,
          fuchsia: colors.fuchsia,
      },
    }
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
