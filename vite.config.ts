import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command, mode }) => {
  // Carrega as variáveis do .env
  const env = loadEnv(mode, process.cwd(), '');

  const isDev = command === 'serve';

  return {
    base: env.VITE_BASE_URL || '/',
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
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './resources/js'),
      },
    },
    define: {
      __VUE_PROD_DEVTOOLS__: true,
    },
    build: {
      outDir: 'public/build',
      emptyOutDir: true,
    },
    server: isDev
      ? {
          host: '0.0.0.0',
          strictPort: true,
          cors: true,
          hmr: {
            host: env.VITE_HMR_HOST,
            protocol: 'ws',
          },
          origin: env.VITE_APP_URL,
          allowedHosts: [
            env.VITE_APP_URL,
            'localhost',
            '127.0.0.1',
          ],
        }
      : undefined,
  };
});
