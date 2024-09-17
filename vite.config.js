import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    base: '/',

    plugins: [
        laravel({
            input: 'resources/js/app.jsx',
            refresh: true,
        }),
        react(),
    ],

    server: {
        host: '0.0.0.0',
        // host: '8d2b-2402-d000-8100-a7f-d923-2f7e-8b84-5425.ngrok-free.app',
        hmr: {
            host: 'localhost',
            // host: '8d2b-2402-d000-8100-a7f-d923-2f7e-8b84-5425.ngrok-free.app'
        },
    },
});

