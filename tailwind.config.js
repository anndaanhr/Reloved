/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#0D9488',
          light: '#54D5C0',
          dark: '#25713C',
          '50': 'rgba(13, 148, 136, 0.1)',
          '300': 'rgba(13, 148, 136, 0.3)',
        },
        background: {
          DEFAULT: '#F8FAFE',
          white: '#FFFFFF',
        },
        text: {
          DEFAULT: '#000000',
          primary: '#111827',
          secondary: '#4B5563',
          tertiary: '#6B7280',
          placeholder: 'rgba(0, 0, 0, 0.5)',
          'gray-48': 'rgba(55, 65, 81, 0.48)',
        },
        border: {
          DEFAULT: '#E5E7EB',
        },
        yellow: {
          bg: '#FEF9C3',
          text: '#854D0E',
        },
        gray: {
          '50': '#F9FAFB',
          '100': '#F3F4F6',
          '200': '#E5E7EB',
          '300': '#D1D5DB',
          '400': '#9CA3AF',
          '500': '#6B7280',
          '600': '#4B5563',
          '700': '#374151',
          '800': '#1F2937',
          '900': '#111827',
        },
      },
      fontFamily: {
        sans: ['Poppins', 'sans-serif'],
      },
      borderRadius: {
        '5': '5px',
        '8': '8px',
        '10': '10px',
        '16': '16px',
        '25': '25px',
        'full': '9999px',
      },
      spacing: {
        '18': '4.5rem', // 72px
        '22': '5.5rem', // 88px
      },
      fontSize: {
        'xs': ['12px', { lineHeight: '1.5' }], // 12px from Figma
        'sm': ['14px', { lineHeight: '1.43' }], // 14px from Figma
        'base': ['15px', { lineHeight: '1.5' }], // 15px from Figma (common in forms)
        'md': ['17px', { lineHeight: '1.5' }], // 17px from Figma
        'lg': ['20px', { lineHeight: '1.5' }], // 20px from Figma
        'xl': ['24px', { lineHeight: '1.5' }], // 24px from Figma
        '2xl': ['33px', { lineHeight: '1.21' }], // 33px from Figma
        '3xl': ['43px', { lineHeight: '1.11' }], // 43px from Figma
      },
      fontWeight: {
        'light': '275',
        'normal': '400',
        'medium': '500',
        'semibold': '600',
        'bold': '700',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'fade-in-right': 'fadeInRight 0.6s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        fadeInRight: {
          '0%': { opacity: '0', transform: 'translateX(20px)' },
          '100%': { opacity: '1', transform: 'translateX(0)' },
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
