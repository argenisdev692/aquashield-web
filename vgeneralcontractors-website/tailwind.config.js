import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Roboto", ...defaultTheme.fontFamily.sans],
            },
            animation: {
                'gradient-border': 'gradient-border 6s ease infinite',
                'border-glow': 'border-glow 4s ease-in-out infinite',
                'table-shadow': 'table-shadow 6s ease-in-out infinite',
                'shimmer': 'shimmer 3s infinite',
                'shimmer-delay-1': 'shimmer 3s infinite 0.2s',
                'shimmer-delay-2': 'shimmer 3s infinite 0.4s',
                'shimmer-delay-3': 'shimmer 3s infinite 0.6s',
                'shimmer-delay-4': 'shimmer 3s infinite 0.8s',
            },
            keyframes: {
                'gradient-border': {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                'border-glow': {
                    '0%, 100%': { boxShadow: '0 0 5px rgba(255, 255, 255, 0.1)' },
                    '50%': { boxShadow: '0 0 15px rgba(255, 255, 255, 0.3)' },
                },
                'table-shadow': {
                    '0%, 100%': { boxShadow: '0 0 15px rgba(138, 43, 226, 0.1), 0 0 30px rgba(138, 43, 226, 0.05)' },
                    '50%': { boxShadow: '0 0 25px rgba(138, 43, 226, 0.2), 0 0 50px rgba(138, 43, 226, 0.1)' },
                },
                'shimmer': {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
            },
        },
    },

    plugins: [forms, typography],
};
