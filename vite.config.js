import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/style.css',
                'resources/css/home.css',

                'resources/js/app.js',
                'resources/js/index.js',
                'resources/js/base.js',
                'resources/js/home.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
                'resources/images/**'
            ],
        }),
    ],
});
