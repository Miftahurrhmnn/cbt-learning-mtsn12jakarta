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
                // Semantic Colors
                primary: {
                    DEFAULT: '#2B77DE',
                    hover: '#1F67CB',
                    light: '#EFF6FF',
                    dark: '#1D4ED8',
                },
                secondary: {
                    DEFAULT: '#64748B',
                    hover: '#475569',
                    light: '#F8FAFC',
                },
                success: {
                    DEFAULT: '#059669',
                    hover: '#047857',
                    light: '#ECFDF5',
                },
                danger: {
                    DEFAULT: '#E11D48',
                    hover: '#BE123C',
                    light: '#FFF1F2',
                },
                warning: {
                    DEFAULT: '#D97706',
                    hover: '#B45309',
                    light: '#FFFBEB',
                },
                info: {
                    DEFAULT: '#0284C7',
                    hover: '#0369A1',
                    light: '#F0F9FF',
                },
                // Role Specific Colors
                guru: {
                    primary: '#4F46E5',
                    'primary-hover': '#4338CA',
                    'primary-light': '#EEF2FF',
                },
                siswa: {
                    primary: '#2B77DE',
                    'primary-hover': '#1F67CB',
                    'primary-light': '#EFF6FF',
                },
            },
        },
    },

    plugins: [forms],
};
