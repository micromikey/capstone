import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/css/map.css', 
                'resources/css/advanced-trail-map.css',
                'resources/js/app.js', 
                'resources/js/map.js', 
                'resources/js/itinerary-map.js',
                'resources/js/advanced-trail-map.js',
                'resources/js/event-poller.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: true,
        outDir: 'public/build',
    },
});
