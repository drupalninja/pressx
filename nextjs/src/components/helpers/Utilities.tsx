import Image from 'next/image';
import { imageStyles, ImageStyleName } from '@/lib/image-styles';

export interface WordPressImage {
  sourceUrl: string;
  altText?: string;
}

export const getImage = (
  media: WordPressImage | null | undefined,
  className?: string,
  style?: ImageStyleName | [ImageStyleName, ImageStyleName]
) => {
  if (!media?.sourceUrl) return null;

  const isSvg = (url: string) => url.endsWith('.svg');

  if (isSvg(media.sourceUrl)) {
    return (
      <Image
        src={media.sourceUrl}
        alt={media.altText ?? ''}
        width={500}
        height={500}
        className={className ?? ''}
        unoptimized
      />
    );
  }

  // Determine desktop and mobile styles
  let desktopStyle: ImageStyleName | undefined;
  let mobileStyle: ImageStyleName | undefined;

  if (Array.isArray(style)) {
    [mobileStyle, desktopStyle] = style;
  } else {
    desktopStyle = style;
  }

  const desktopImageStyle = desktopStyle ? imageStyles[desktopStyle] : null;
  const mobileImageStyle = mobileStyle ? imageStyles[mobileStyle] : null;

  if (!desktopImageStyle) return null;

  const quality = desktopImageStyle.quality ?? 90;
  const priority = desktopStyle?.startsWith('hero') ?? false;

  // If an array of styles is provided, render responsive images
  if (Array.isArray(style)) {
    return (
      <>
        {mobileStyle && mobileImageStyle && (
          <div className="block md:hidden">
            <Image
              src={`/api/image?url=${encodeURIComponent(media.sourceUrl)}&width=${mobileImageStyle.width}&height=${mobileImageStyle.height}&quality=${mobileImageStyle.quality ?? 90}&style=${mobileStyle}`}
              alt={media.altText ?? ''}
              width={mobileImageStyle.width}
              height={mobileImageStyle.height}
              className={`${className}`}
              quality={mobileImageStyle.quality ?? 90}
              priority={priority}
              sizes="100vw"
            />
          </div>
        )}
        <div className="hidden md:block">
          <Image
            src={`/api/image?url=${encodeURIComponent(media.sourceUrl)}&width=${desktopImageStyle.width}&height=${desktopImageStyle.height}&quality=${quality}&style=${desktopStyle}`}
            alt={media.altText ?? ''}
            width={desktopImageStyle.width}
            height={desktopImageStyle.height}
            className={`${className}`}
            quality={quality}
            priority={priority}
            sizes="100vw"
          />
        </div>
      </>
    );
  } else {
    // Fallback for single style usage
    const src = `/api/image?url=${encodeURIComponent(media.sourceUrl)}&width=${desktopImageStyle.width}&height=${desktopImageStyle.height}&quality=${quality}&style=${desktopStyle}`;
    return (
      <Image
        src={src}
        alt={media.altText ?? ''}
        width={desktopImageStyle.width}
        height={desktopImageStyle.height}
        className={`${className} max-w-full h-auto`}
        sizes={`${desktopImageStyle.width}px`}
        quality={quality}
        priority={priority}
      />
    );
  }
};
