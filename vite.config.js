import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

const ddevPrimaryUrl = process.env.DDEV_PRIMARY_URL_WITHOUT_PORT ?? 'https://blogy.ddev.site';

export default defineConfig({
    plugins: [tailwindcss()],
    publicDir: false,
    build: {
        manifest: true,
        outDir: 'public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: `${ddevPrimaryUrl}:5173`,
        cors: {
            origin: /https?:\/\/([A-Za-z0-9-.]+)?(\.ddev\.site)(?::\d+)?$/,
        },
    },
});
