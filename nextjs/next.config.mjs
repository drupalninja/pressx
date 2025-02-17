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
}

export default nextConfig;
