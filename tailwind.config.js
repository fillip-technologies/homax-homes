import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            // Moved here from the per-page `tailwind.config = {...}` scripts that only
            // worked while the Tailwind Play CDN was loaded.
            colors: {
                primary: {
                    DEFAULT: '#000080',
                    dark: '#000066',
                    darker: '#000033',
                },
                'primary-dark': '#000066',
                'primary-darker': '#000033',
                brand: {
                    primary: '#000080',
                    secondary: '#000066',
                    light: '#FFFFFF',
                    dark: '#F7F6FF',
                },
                accent: {
                    DEFAULT: '#DAA520',
                    dark: '#B8860B',
                },
                textClr: {
                    primary: '#111827',
                    secondary: '#5F6472',
                    accent: '#000080',
                },
            },
            fontFamily: {
                sans: ['Mulish', ...defaultTheme.fontFamily.sans],
                serif: ['Mulish', ...defaultTheme.fontFamily.sans],
                display: ['Mulish', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                property: '0 10px 30px rgba(0, 0, 128, 0.1)',
                'property-hover': '0 15px 40px rgba(0, 0, 128, 0.18)',
            },
            animation: {
                'fade-in-up': 'fadeInUp 0.8s ease-out forwards',
                'slide-in-left': 'slideInLeft 0.8s ease-out forwards',
                'subtle-pulse': 'subtlePulse 2s infinite ease-in-out',
            },
            keyframes: {
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                subtlePulse: {
                    '0%, 100%': { transform: 'scale(1)', boxShadow: '0 0 0 0 rgba(0, 0, 128, 0.28)' },
                    '50%': { transform: 'scale(1.02)', boxShadow: '0 0 0 10px rgba(0, 0, 128, 0)' },
                },
            },
        },
    },
    plugins: [
        function ({ addComponents }) {
            addComponents({
                '.pagination': { '@apply flex items-center space-x-2': {} },
                '.page-item': { '@apply px-4 py-2 border rounded': {} },
                '.page-item.active': { '@apply border-[#000080] bg-[#000080] text-white': {} },
                '.page-item:not(.active)': { '@apply border-gray-300 text-gray-700 hover:bg-gray-100': {} },
                '.page-item.disabled': { '@apply opacity-50 cursor-not-allowed': {} },
            });
        },
    ],
};
