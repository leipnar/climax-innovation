import { defineConfig } from 'astro/config';
import tailwindcss from '@tailwindcss/vite';
import sitemap from '@astrojs/sitemap';

export default defineConfig({
  site: 'https://climaxinnovation.com',
  vite: {
    plugins: [tailwindcss()]
  },
  integrations: [sitemap()],
  output: 'static',
  build: {
    inlineStylesheets: 'auto'
  }
});
