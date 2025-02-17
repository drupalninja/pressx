import { imageStyles } from './image-styles';

export default function loader({ src, width, quality }: { src: string; width: number; quality?: number }) {
  // If the source is an SVG or starts with a relative path, return it as-is
  if (src.endsWith('.svg') || src.startsWith('/')) {
    return src;
  }

  // Parse the style from the query string
  const [path, query] = src.split('?');
  const params = new URLSearchParams(query);
  const styleName = params.get('style');

  // Get style configuration if available
  const style = styleName && styleName in imageStyles
    ? imageStyles[styleName as keyof typeof imageStyles]
    : null;

  if (style) {
    // Use the exact dimensions from the style
    const imageParams = new URLSearchParams({
      url: path,
      width: style.width.toString(),
      height: style.height.toString(),
      quality: (style.quality || quality || 90).toString(),
    });

    return `/api/image?${imageParams.toString()}`;
  }

  // If no style is specified, use the requested width and maintain aspect ratio
  const aspectRatio = width <= 767 ? 4 / 3 : 2 / 1;  // 4:3 for mobile, 2:1 for desktop
  const imageHeight = Math.round(width * aspectRatio);

  const imageParams = new URLSearchParams({
    url: path,
    width: width.toString(),
    height: imageHeight.toString(),
    quality: (quality || 90).toString(),
  });

  return `/api/image?${imageParams.toString()}`;
}
