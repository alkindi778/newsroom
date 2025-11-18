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
        chunkSizeWarningLimit: 1000, // Increase limit to 1000 KB to suppress warnings
        rollupOptions: {
            output: {
                // تقسيم TinyMCE لـ chunk منفصل مع cache طويل الأمد
                manualChunks: {
                    tinymce: ['tinymce']
                }
            }
        }
    },
});
