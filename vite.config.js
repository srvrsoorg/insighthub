import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
export default defineConfig({
    server: {
        watch: {
            // Tell Vite to ignore changes to files or directories containing environment variables
            ignored: [
                '**/.env'
            ],
        },
    },
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/bootstrapTheme.js'],
            refresh: true,
        }),
    ],
});