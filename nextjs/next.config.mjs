/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    domains: ['pressx.ddev.site'],
    formats: ['image/avif', 'image/webp'],
    deviceSizes: [720, 1280, 1920, 3840],
    imageSizes: [400, 800, 1200],
    loader: 'custom',
    loaderFile: './src/lib/image-loader.ts',
  },
  publicRuntimeConfig: {
    LOGO_URL: process.env.LOGO_URL || '/images/logo.svg',
    LOGO_WIDTH: process.env.LOGO_WIDTH || '160',
    LOGO_HEIGHT: process.env.LOGO_HEIGHT || '42',
    SITE_NAME: process.env.SITE_NAME || 'PressX',
    SHOW_LOGO: process.env.SHOW_LOGO || '1',
  },
}

export default nextConfig;
