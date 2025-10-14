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
                'resources/js/event-poller.js',
                'resources/js/trail-3d-map.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        manifest: 'manifest.json',  // Put manifest at root of build dir, not in .vite subdir
        outDir: 'public/build',
    },
});
