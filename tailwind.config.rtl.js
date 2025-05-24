/**
 * Tailwind RTL Configuration
 * file path: tailwind.config.rtl.js
 */

module.exports = {
  theme: {
    extend: {
      // RTL specific extensions can be added here
      spacing: {
        // Mirror spacing for RTL if needed
      },
    },
  },
  plugins: [
    // RTL plugin configuration
    function({ addUtilities }) {
      const newUtilities = {
        '.text-start': {
          'text-align': 'start',
        },
        '.text-end': {
          'text-align': 'end',
        },
        '.float-start': {
          'float': 'inline-start',
        },
        '.float-end': {
          'float': 'inline-end',
        },
        '.me-auto': {
          'margin-inline-end': 'auto',
        },
        '.ms-auto': {
          'margin-inline-start': 'auto',
        },
        '.me-0': {
          'margin-inline-end': '0',
        },
        '.ms-0': {
          'margin-inline-start': '0',
        },
        // Add more RTL utilities as needed
      };

      addUtilities(newUtilities, ['responsive']);
    },
  ],
};
