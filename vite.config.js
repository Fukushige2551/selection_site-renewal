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
                main: path.resolve(__dirname, 'src/js/main.js')
            }
        }
    }
})