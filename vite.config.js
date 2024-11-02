import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
        // Remove the Vue plugin since Vue is no longer used
    ],
    resolve: {
        // Remove the Vue alias as well
        alias: {
            // Commenting out the Vue alias since it won't be used
            // vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});





