// ---------------------------------------------------------------------
// Merge this into the `theme.extend.colors` section of your existing
// tailwind.config.js
// ---------------------------------------------------------------------

module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#DC2626', // red — the "accept / act now" color: buttons, links, emphasis
          content: '#FFFFFF',
        },
        secondary: {
          DEFAULT: '#D4AF37', // gold — accents, borders, the "declining" button outline, celebratory bits
          content: '#3A2E00',
        },
        tertiary: {
          DEFAULT: '#FFFFFF', // white — page/card background
          content: '#1F2937', // near-black, used for body text on the white background
        },
      },
      keyframes: {
        wiggle: {
          '0%, 100%': { transform: 'translateX(0)' },
          '50%': { transform: 'translateX(-4px)' },
        },
      },
    },
  },
}
