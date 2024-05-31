import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],

    server: {
        host: '0.0.0.0',
        // host: 'https://bfde-2402-d000-8100-47f-88ec-b101-5833-d8f3.ngrok-free.app/',
        hmr: {
            host: 'localhost'
            // host: 'https://bfde-2402-d000-8100-47f-88ec-b101-5833-d8f3.ngrok-free.app/'
        },
    },

});

