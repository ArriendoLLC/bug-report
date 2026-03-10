import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
  plugins: [vue()],
  build: {
    lib: {
      entry: resolve(__dirname, 'resources/js/index.js'),
      name: 'BugReport',
      fileName: (format) => `bug-report.${format}.js`
    },
    rollupOptions: {
      // Make sure to externalize deps that shouldn't be bundled
      external: ['vue', 'axios', 'vue-i18n'],
      output: {
        // Provide global variables to use in the UMD build
        globals: {
          vue: 'Vue',
          axios: 'axios',
          'vue-i18n': 'VueI18n'
        },
        exports: 'named'
      }
    },
    outDir: 'dist'
  }
});
