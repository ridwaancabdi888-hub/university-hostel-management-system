import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Geist', ...defaultTheme.fontFamily.sans],
            },

            // "Academic Sanctuary" design system — see DESIGN.md in the
            // Stitch export this was imported from. Light-mode values are
            // taken verbatim from that spec; the "night-*" tokens are a
            // dark-mode palette derived from the same brand (not part of
            // the original spec, which only defined light mode) to slot
            // into this app's existing `dark:` utility convention.
            colors: {
                surface: '#fbf8ff',
                'surface-dim': '#dad9e3',
                'surface-bright': '#fbf8ff',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f4f2fc',
                'surface-container': '#eeedf7',
                'surface-container-high': '#e8e7f1',
                'surface-container-highest': '#e3e1eb',
                'surface-variant': '#e3e1eb',
                'surface-tint': '#3755c3',
                'on-surface': '#1a1b22',
                'on-surface-variant': '#444653',
                'inverse-surface': '#2f3037',
                'inverse-on-surface': '#f1f0fa',
                outline: '#757684',
                'outline-variant': '#c4c5d5',
                primary: '#00288e',
                'on-primary': '#ffffff',
                'primary-container': '#1e40af',
                'on-primary-container': '#a8b8ff',
                'inverse-primary': '#b8c4ff',
                'primary-fixed': '#dde1ff',
                'primary-fixed-dim': '#b8c4ff',
                'on-primary-fixed': '#001453',
                'on-primary-fixed-variant': '#173bab',
                secondary: '#516070',
                'on-secondary': '#ffffff',
                'secondary-container': '#d5e4f8',
                'on-secondary-container': '#576676',
                'secondary-fixed': '#d5e4f8',
                'secondary-fixed-dim': '#b9c8db',
                'on-secondary-fixed': '#0e1d2b',
                'on-secondary-fixed-variant': '#3a4858',
                tertiary: '#611e00',
                'on-tertiary': '#ffffff',
                'tertiary-container': '#872d00',
                'on-tertiary-container': '#ffa583',
                'tertiary-fixed': '#ffdbce',
                'tertiary-fixed-dim': '#ffb59a',
                'on-tertiary-fixed': '#380d00',
                'on-tertiary-fixed-variant': '#802a00',
                error: '#ba1a1a',
                'on-error': '#ffffff',
                'error-container': '#ffdad6',
                'on-error-container': '#93000a',
                background: '#fbf8ff',
                'on-background': '#1a1b22',

                // Dark-mode counterparts (used as `dark:bg-night-*` etc.)
                'night-bg': '#12161d',
                'night-surface': '#171d26',
                'night-surface-high': '#1c232e',
                'night-border': '#2a313d',
                'night-on-surface': '#e5e7eb',
                'night-on-surface-variant': '#9aa3b2',
                'night-primary': '#8ea2ff',
                'night-on-primary': '#0a1230',
                'night-secondary-container': '#22314a',
                'night-on-secondary-container': '#b8c4ff',
                'night-error': '#ff9a92',
                'night-error-container': '#4a1414',
            },

            borderRadius: {
                sm: '0.5rem',
                DEFAULT: '1rem',
                md: '1.5rem',
                lg: '2rem',
                xl: '3rem',
            },

            spacing: {
                base: '8px',
                xs: '4px',
                sm: '12px',
                md: '24px',
                lg: '40px',
                xl: '64px',
                gutter: '24px',
                'margin-mobile': '16px',
                'margin-desktop': '48px',
            },

            fontSize: {
                display: ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'headline-lg': ['32px', { lineHeight: '40px', letterSpacing: '-0.01em', fontWeight: '600' }],
                'headline-lg-mobile': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'headline-sm': ['20px', { lineHeight: '28px', fontWeight: '600' }],
                'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'label-md': ['14px', { lineHeight: '20px', letterSpacing: '0.01em', fontWeight: '500' }],
                'label-sm': ['12px', { lineHeight: '16px', fontWeight: '600' }],
            },
        },
    },

    plugins: [forms],
};
