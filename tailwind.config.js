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
            colors: {
                aito: {
                    red: '#E11D48', // A punchy, modern red
                    dark: '#111827', // A deep charcoal (better than pure black for UI)
                },
            },
        },
    },

    plugins: [forms],
};
