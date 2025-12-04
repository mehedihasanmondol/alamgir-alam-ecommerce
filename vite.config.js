import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '0.0.0.0',        // allow network access
        port: 5173,             // or any port you like
        hmr: {
            host: '192.168.0.118', // Change this to your computer's local IP when needed (e.g., 192.168.0.118)
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js', 
                'resources/js/admin.js',
                'resources/js/email-editor.js',
                'resources/js/ckeditor-init.js',  // CKEditor initialization
                'resources/js/blog-post-editor.js',  // Blog post editor
                'resources/js/product-editor.js',  // Product editor
                'resources/js/footer-settings-editor.js',  // Footer settings editor
                'resources/js/site-settings-editor.js'  // Site settings editor
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
