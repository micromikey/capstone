import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        {
            pattern: /from-(yellow|gray|blue|indigo|orange|teal|green)-\d{3}/,
        },
        {
            pattern: /to-(yellow|gray|blue|indigo|orange|teal|green)-\d{3}/,
        },
        {
            pattern: /(absolute|relative|right-\d+|bottom-\d+|z-\d+)/,
        },
        {
            pattern: /w-(1\/2|full)|h-full/,
        },
        {
            pattern: /opacity-\d+/,
        },
        {
            pattern: /fill-(current|white|gray|green|mountain-\d{3})/,
        },
        {
            pattern: /fill-mountain-(100|200|300)/,
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                mountain: {
                    100: '#a3c9a8',
                    200: '#8cb398',
                    300: '#6e9e7c',
                },
            },
            boxShadow: {
                '3xl': '0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.25)',
            },
            opacity: {
                98: '0.98',
            },
        },
    },

    plugins: [forms, typography],
}
