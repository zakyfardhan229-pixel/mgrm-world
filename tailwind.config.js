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
                ink: {
                    DEFAULT: '#0a0a0a',
                    soft: '#1c1c1e',
                    muted: '#3f3f46',
                },
                paper: '#fafafa',
            },
            boxShadow: {
                card: '0 4px 20px rgba(0, 0, 0, 0.06)',
                hover: '0 12px 32px rgba(0, 0, 0, 0.14)',
                elevated: '0 20px 50px rgba(0, 0, 0, 0.18)',
            },
        },
    },

    plugins: [forms],
};
