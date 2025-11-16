import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor libraries
                    'vendor': ['alpinejs'],
                    // Add more chunks as needed
                }
            }
        },
        chunkSizeWarningLimit: 1000, // Increase limit to 1000 KB
    },
});
