/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue",
  ],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '1.5rem',
        lg: '2rem',
        xl: '3rem',
        '2xl': '4rem',
      },
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1200px',
        '2xl': '1320px',
      },
    },
    extend: {
      fontFamily: {
        sans: ['Azer', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'sans-serif'],
      },
      colors: {
        // ألوان الشعار الرسمية
        primary: {
          DEFAULT: '#D4AF37', // ذهبي
          50: '#FAF7ED',
          100: '#F5EFDB',
          200: '#EBDFB7',
          300: '#E1CF93',
          400: '#D7BF6F',
          500: '#D4AF37', // الذهبي الأساسي
          600: '#B6962D',
          700: '#8A7122',
          800: '#5D4C17',
          900: '#2F260B',
        },
        navy: {
          DEFAULT: '#1e2a4a', // أزرق داكن
          50: '#E8EAF0',
          100: '#D1D5E1',
          200: '#A3ABC3',
          300: '#7581A5',
          400: '#475787',
          500: '#1e2a4a', // الأزرق الداكن الأساسي
          600: '#18223E',
          700: '#12192F',
          800: '#0C111F',
          900: '#060810',
        },
        dark: {
          DEFAULT: '#0F2027', // الخلفية الداكنة
          50: '#E6E8E9',
          100: '#CDD1D3',
          200: '#9BA3A7',
          300: '#69757B',
          400: '#37474F',
          500: '#0F2027', // الداكن الأساسي
          600: '#0C1A20',
          700: '#091318',
          800: '#060D10',
          900: '#030608',
        },
        gold: {
          DEFAULT: '#C9A961', // ذهبي فاتح
          50: '#FAF6EC',
          100: '#F5EDD9',
          200: '#EBDBB3',
          300: '#E1C98D',
          400: '#D7B767',
          500: '#C9A961', // الذهبي الفاتح الأساسي
          600: '#B08F43',
          700: '#846B32',
          800: '#584721',
          900: '#2C2411',
        },
        accent: {
          DEFAULT: '#E31E24', // أحمر (من العلم)
          50: '#FEE9EA',
          100: '#FDD3D5',
          200: '#FBA7AB',
          300: '#F97B81',
          400: '#F74F57',
          500: '#E31E24', // الأحمر الأساسي
          600: '#B6181D',
          700: '#881216',
          800: '#5A0C0F',
          900: '#2D0607',
        },
      },
    },
  },
  plugins: [
    // دعم RTL/LTR
    function({ addVariant }) {
      addVariant('rtl', 'html[dir="rtl"] &')
      addVariant('ltr', 'html[dir="ltr"] &')
    },
  ],
}
