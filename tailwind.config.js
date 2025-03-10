const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                serif: ['Source Serif Pro', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                editor: {
                    50: '#f8f9fa',
                    100: '#f1f3f5',
                    200: '#e9ecef',
                    300: '#dee2e6',
                    400: '#ced4da',
                    500: '#adb5bd',
                    600: '#868e96',
                    700: '#495057',
                    800: '#343a40',
                    900: '#212529',
                },
                dark: {
                    50: '#f8f9fa',
                    100: '#f1f3f5',
                    200: '#e9ecef',
                    300: '#dee2e6',
                    400: '#ced4da',
                    500: '#adb5bd',
                    600: '#868e96',
                    700: '#495057',
                    800: '#343a40',
                    900: '#212529',
                },
            },
            typography: theme => ({
                DEFAULT: {
                    css: {
                        maxWidth: '65ch',
                        color: theme('colors.gray.900'),
                        p: {
                            lineHeight: '1.8',
                        },
                        'h1, h2, h3, h4': {
                            letterSpacing: '-0.025em',
                        },
                    },
                },
                dark: {
                    css: {
                        color: theme('colors.gray.100'),
                        'h1, h2, h3, h4': {
                            color: theme('colors.gray.100'),
                        },
                        p: {
                            color: theme('colors.gray.300'),
                        },
                        strong: {
                            color: theme('colors.gray.100'),
                        },
                        a: {
                            color: theme('colors.indigo.400'),
                            '&:hover': {
                                color: theme('colors.indigo.300'),
                            },
                        },
                        blockquote: {
                            color: theme('colors.gray.300'),
                            borderLeftColor: theme('colors.gray.700'),
                        },
                        hr: {
                            borderColor: theme('colors.gray.700'),
                        },
                        ol: {
                            li: {
                                '&:before': {
                                    color: theme('colors.gray.500'),
                                },
                            },
                        },
                        ul: {
                            li: {
                                '&:before': {
                                    backgroundColor: theme('colors.gray.500'),
                                },
                            },
                        },
                        code: {
                            color: theme('colors.gray.100'),
                        },
                        pre: {
                            color: theme('colors.gray.200'),
                            backgroundColor: theme('colors.gray.800'),
                        },
                        thead: {
                            color: theme('colors.gray.100'),
                            borderBottomColor: theme('colors.gray.700'),
                        },
                        tbody: {
                            tr: {
                                borderBottomColor: theme('colors.gray.700'),
                            },
                        },
                    },
                },
            }),
            spacing: {
                '128': '32rem',
                '144': '36rem',
            },
            opacity: {
                '15': '0.15',
                '35': '0.35',
                '85': '0.85',
            },
            lineHeight: {
                'extra-loose': '2.5',
                '12': '3rem',
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('@tailwindcss/aspect-ratio'),
        require('@tailwindcss/line-clamp'),
    ],
};
