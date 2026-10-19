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
            usePolling: true,
            ignored: [
                '**/bk_foods-selection.co.jp/**',
                '**/bk_foods-selection.co.jp.testrs.jp/**',
                '**/stg-upload/**',
                '**/_local_bk/**'
            ]
        }
    },
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                'cf7-layout': path.resolve(__dirname, 'src/css/cf7-layout.css'),
                'contact-responsive': path.resolve(__dirname, 'src/css/contact-responsive.css'),
                main: path.resolve(__dirname, 'src/js/main.js'),
                'front-page': path.resolve(__dirname, 'src/js/front-page.js'),
                'page-contact': path.resolve(__dirname, 'src/js/page-contact.js'),
                'page-contact-thanks': path.resolve(__dirname, 'src/js/page-contact-thanks.js'),
                'page-privacy': path.resolve(__dirname, 'src/js/page-privacy.js'),
                'page-shop': path.resolve(__dirname, 'src/js/page-shop.js'),
                'page-select': path.resolve(__dirname, 'src/js/page-select.js'),
                'page-select-vegetables-fruit': path.resolve(__dirname, 'src/js/page-select-vegetables-fruit.js'),
                'page-select-shop': path.resolve(__dirname, 'src/js/page-select-shop.js'),
                'page-select-meat': path.resolve(__dirname, 'src/js/page-select-meat.js'),
                'page-select-fish': path.resolve(__dirname, 'src/js/page-select-fish.js'),
                'page-select-rice': path.resolve(__dirname, 'src/js/page-select-rice.js'),
                'page-select-deli': path.resolve(__dirname, 'src/js/page-select-deli.js'),
                'page-company': path.resolve(__dirname, 'src/js/page-company.js'),
                'header-company': path.resolve(__dirname, 'src/js/header-company.js'),
                'footer-company': path.resolve(__dirname, 'src/js/footer-company.js'),
                'page-company-about': path.resolve(__dirname, 'src/js/page-company-about.js'),
                'page-company-business': path.resolve(__dirname, 'src/js/page-company-business.js'),
                'page-company-business-form': path.resolve(__dirname, 'src/js/page-company-business-form.js'),
                'page-job': path.resolve(__dirname, 'src/js/page-job.js'),
                'page-job-search': path.resolve(__dirname, 'src/js/page-job-search.js'),
                'page-select-washoku-daily': path.resolve(__dirname, 'src/js/page-select-washoku-daily.js'),
                'page-select-foods': path.resolve(__dirname, 'src/js/page-select-foods.js'),
                'page-select-sweets': path.resolve(__dirname, 'src/js/page-select-sweets.js'),
                'page-select-alcohol': path.resolve(__dirname, 'src/js/page-select-alcohol.js'),
                'page-recruit-interview': path.resolve(__dirname, 'src/js/page-recruit-interview.js'),
                'page-recruit': path.resolve(__dirname, 'src/js/page-recruit.js'),
                'page-recruit-work': path.resolve(__dirname, 'src/js/page-recruit-work.js'),
                'page-recruit-entry': path.resolve(__dirname, 'src/js/page-recruit-entry.js'),
                'single-shop': path.resolve(__dirname, 'src/js/single-shop.js'),
                'archive-news': path.resolve(__dirname, 'src/js/archive-news.js'),
                'single-news': path.resolve(__dirname, 'src/js/single-news.js'),
                'single-recruit-part-time': path.resolve(__dirname, 'src/js/single-recruit-part-time.js'),
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



