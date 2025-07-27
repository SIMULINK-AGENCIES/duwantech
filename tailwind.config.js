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
            animation: {
                'fade-in': 'fadeIn 0.6s ease-out',
                'fade-in-up': 'fadeInUp 0.6s ease-out',
                'slide-in-right': 'slideInRight 0.6s ease-out',
                'slide-in-left': 'slideInLeft 0.6s ease-out',
                'slide-in-up': 'slideInUp 0.6s ease-out',
                'slide-in-down': 'slideInDown 0.6s ease-out',
                'scale-in': 'scaleIn 0.4s ease-out',
                'bounce-in': 'bounceIn 0.6s ease-out',
                'rotate-in': 'rotateIn 0.6s ease-out',
                'flip-in': 'flipIn 0.6s ease-out',
                'pulse-slow': 'pulse 3s infinite',
                'spin-slow': 'spin 3s linear infinite',
                'wiggle': 'wiggle 1s ease-in-out infinite',
                'shake': 'shake 0.5s ease-in-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { opacity: '0', transform: 'translateX(100px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { opacity: '0', transform: 'translateX(-100px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                slideInUp: {
                    '0%': { opacity: '0', transform: 'translateY(100px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-100px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                bounceIn: {
                    '0%': { opacity: '0', transform: 'scale(0.3)' },
                    '50%': { opacity: '1', transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                rotateIn: {
                    '0%': { opacity: '0', transform: 'rotate(-200deg)' },
                    '100%': { opacity: '1', transform: 'rotate(0)' },
                },
                flipIn: {
                    '0%': { opacity: '0', transform: 'rotateY(-90deg)' },
                    '100%': { opacity: '1', transform: 'rotateY(0)' },
                },
                wiggle: {
                    '0%, 100%': { transform: 'rotate(-3deg)' },
                    '50%': { transform: 'rotate(3deg)' },
                },
                shake: {
                    '0%, 100%': { transform: 'translateX(0)' },
                    '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-10px)' },
                    '20%, 40%, 60%, 80%': { transform: 'translateX(10px)' },
                },
            },
            transitionTimingFunction: {
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
            boxShadow: {
                'glow': '0 0 20px rgba(59, 130, 246, 0.5)',
                'glow-green': '0 0 20px rgba(34, 197, 94, 0.5)',
                'glow-red': '0 0 20px rgba(239, 68, 68, 0.5)',
                'glow-purple': '0 0 20px rgba(147, 51, 234, 0.5)',
            },
            backdropBlur: {
                'xs': '2px',
            },
        },
    },

    plugins: [
        forms,
        // Custom utilities for animations
        function({ addUtilities }) {
            const newUtilities = {
                '.animate-delay-100': {
                    'animation-delay': '0.1s',
                },
                '.animate-delay-200': {
                    'animation-delay': '0.2s',
                },
                '.animate-delay-300': {
                    'animation-delay': '0.3s',
                },
                '.animate-delay-500': {
                    'animation-delay': '0.5s',
                },
                '.animate-delay-700': {
                    'animation-delay': '0.7s',
                },
                '.animate-delay-1000': {
                    'animation-delay': '1s',
                },
                '.animation-fill-both': {
                    'animation-fill-mode': 'both',
                },
                '.animation-fill-forwards': {
                    'animation-fill-mode': 'forwards',
                },
                '.backface-hidden': {
                    'backface-visibility': 'hidden',
                },
                '.transform-gpu': {
                    'transform': 'translateZ(0)',
                },
                '.animate-on-hover:hover': {
                    'animation-play-state': 'running',
                },
                '.animate-paused': {
                    'animation-play-state': 'paused',
                },
            }
            addUtilities(newUtilities);
        }
    ],
};
