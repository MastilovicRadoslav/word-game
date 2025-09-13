// vite.config.js
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 5173,
    proxy: {
      // sve što ide na /api preusmjeri na Symfony na 8000
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        // ako koristiš putanje poput /api/words/score OK je bez rewrites,
        // ali evo primjer kad je potrebno:
        // rewrite: (path) => path.replace(/^\/api/, '/api'),
      },
    },
  },
})
