import Image from 'next/image';

interface ImageSize {
  name: string;
  sourceUrl: string;
  width: number;
  height: number;
}

export interface WordPressImage {
  sourceUrl: string;
  altText?: string;
  mediaDetails?: {
    width: number;
    height: number;
    sizes: ImageSize[];
  };
}

const defaultSizes = '(max-width: 767px) 100vw, 50vw';

export const getImage = (
  media: WordPressImage | null | undefined,
  className?: string,
  imageStyle?: string | string[],
  sizes?: string
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

  // Get all available sizes
  const availableSizes = media.mediaDetails?.sizes ?? [];

  // If imageStyle is a string, use it for all screen sizes
  if (typeof imageStyle === 'string') {
    const variant = availableSizes.find(size => size.name === imageStyle);
    if (variant?.sourceUrl) {
      return (
        <Image
          src={variant.sourceUrl}
          alt={media.altText ?? ''}
          width={variant.width}
          height={variant.height}
          className={className ?? ''}
          sizes={sizes ?? defaultSizes}
          unoptimized
        />
      );
    }
  }

  // If imageStyle is an array, use first for mobile and second for desktop
  if (Array.isArray(imageStyle) && imageStyle.length >= 2) {
    const mobileVariant = availableSizes.find(size => size.name === imageStyle[0]);
    const desktopVariant = availableSizes.find(size => size.name === imageStyle[1]);

    if (mobileVariant?.sourceUrl && desktopVariant?.sourceUrl) {
      return (
        <picture>
          <source
            media="(max-width: 767px)"
            srcSet={`${mobileVariant.sourceUrl} ${mobileVariant.width}w`}
          />
          <source
            media="(min-width: 768px)"
            srcSet={`${desktopVariant.sourceUrl} ${desktopVariant.width}w`}
          />
          <Image
            src={media.sourceUrl}
            alt={media.altText ?? ''}
            width={media.mediaDetails?.width ?? 1920}
            height={media.mediaDetails?.height ?? 955}
            className={className ?? ''}
            sizes={sizes ?? defaultSizes}
            unoptimized
          />
        </picture>
      );
    }
  }

  // Fallback to original image
  return (
    <Image
      src={media.sourceUrl}
      alt={media.altText ?? ''}
      width={media.mediaDetails?.width ?? 1920}
      height={media.mediaDetails?.height ?? 955}
      className={className ?? ''}
      sizes={sizes ?? defaultSizes}
      unoptimized
    />
  );
};
