import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react-swc'

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [react()],
    build: {
        cssCodeSplit: false,
        outFile: './dist',
        rollupOptions: {
            output: {
                manualChunks: undefined,
                dir: './dist',
                entryFileNames: 'main.js',
                chunkFileNames: 'main.js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css')
                        return 'main.css';

                    return assetInfo.name;
                },
            },
        },
    }
})
