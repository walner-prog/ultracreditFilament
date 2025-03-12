import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'vendor/filament/filament/resources/css/theme.css' // Asegura los estilos de Filament
            ],
            refresh: true,
        }),
    ],
});
