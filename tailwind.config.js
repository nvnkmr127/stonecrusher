import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#0f172a', // Slate 900
                    light: '#334155',   // Slate 700
                    dark: '#020617',    // Slate 950
                },
                secondary: {
                    DEFAULT: '#64748b', // Slate 500
                    light: '#94a3b8',   // Slate 400
                },
                background: '#f8fafc', // Slate 50
                surface: '#ffffff',
                border: '#e2e8f0',     // Slate 200
            },
            boxShadow: {
                DEFAULT: '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                sm: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            }
        },
    },

    plugins: [],
};
