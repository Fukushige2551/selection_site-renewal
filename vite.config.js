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
                'page-contact': path.resolve(__dirname, 'src/js/page-contact.js'),
                'page-contact-thanks': path.resolve(__dirname, 'src/js/page-contact-thanks.js'),
                'page-privacy': path.resolve(__dirname, 'src/js/page-privacy.js'),
                'page-shop': path.resolve(__dirname, 'src/js/page-shop.js'),
                'page-select': path.resolve(__dirname, 'src/js/page-select.js'),
                'page-select-vegetables-fruit': path.resolve(__dirname, 'src/js/page-select-vegetables-fruit.js'),
                'page-select-meat': path.resolve(__dirname, 'src/js/page-select-meat.js'),
                'page-select-fish': path.resolve(__dirname, 'src/js/page-select-fish.js'),
                'page-select-rice': path.resolve(__dirname, 'src/js/page-select-rice.js'),
                'page-select-deli': path.resolve(__dirname, 'src/js/page-select-deli.js'),
                'page-select-washoku-daily': path.resolve(__dirname, 'src/js/page-select-washoku-daily.js'),
                'page-select-foods': path.resolve(__dirname, 'src/js/page-select-foods.js'),
                'page-select-sweets': path.resolve(__dirname, 'src/js/page-select-sweets.js'),
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



