import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from "@tailwindcss/vite";
import { resolve } from 'node:path';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    esbuild: {
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
    },
    build: {
        sourcemap: false,
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1200,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('vue')) return 'vendor-vue';
                        if (id.includes('@inertiajs')) return 'vendor-inertia';
                        if (id.includes('vue-router')) return 'vendor-router';
                        if (id.includes('axios')) return 'vendor-axios';
                        if (id.includes('date-fns')) return 'vendor-date';
                        if (id.includes('vee-validate') || id.includes('zod')) return 'vendor-forms';
                        if (id.includes('@vueuse')) return 'vendor-vueuse';
                        if (id.includes('lucide')) return 'vendor-icons';
                        if (id.includes('@tanstack')) return 'vendor-table';
                        return 'vendor';
                    }
                },
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
});
