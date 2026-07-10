import { defineConfig } from 'vite'
import path from 'path'
import FullReload from 'vite-plugin-full-reload'

export default defineConfig({
    root: '.',
    base: '',
    plugins: [
        FullReload([
            '**/*.php',
            'template-parts/**/*.php'
        ])
    ],
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: true,
        cors: true,
        watch: {
            usePolling: true
        }
    },
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'src/js/main.js'),
                'front-page': path.resolve(__dirname, 'src/js/front-page.js'),
                'page-shop': path.resolve(__dirname, 'src/js/page-shop.js'),
                'single-shop': path.resolve(__dirname, 'src/js/single-shop.js'),
                'archive-news': path.resolve(__dirname, 'src/js/archive-news.js'),
                'single-news': path.resolve(__dirname, 'src/js/single-news.js'),
                'archive-recipe': path.resolve(__dirname, 'src/js/archive-recipe.js'),
                'single-recipe': path.resolve(__dirname, 'src/js/single-recipe.js')
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: (assetInfo) => {
                    const ext = path.extname(assetInfo.name || '')
                    return 'assets/[name][extname]'
                }
            }
        }
    }
})

