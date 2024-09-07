import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {(tailwindConfig: object) => object} */

const withMT = require("@material-tailwind/react/utils/withMT");

export default withMT({
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        require("@xpd/tailwind-3dtransforms"),
        require('tailwind-scrollbar'),
    ],
});
