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

type ImageStyleConfig = {
  mobile?: string;
  desktop: string;
  sizes?: string;
};

const defaultSizes = '(max-width: 640px) 640px, (max-width: 960px) 960px, 1280px';

export const getImage = (
  media: WordPressImage | null | undefined,
  className?: string,
  styleConfig?: ImageStyleConfig | string
) => {
  if (!media?.sourceUrl) return null;

  const config = typeof styleConfig === 'string'
    ? { desktop: styleConfig }
    : styleConfig;

  const getSize = (name: string) =>
    media?.mediaDetails?.sizes?.find((size) => size.name === name);

  const isSvg = (url: string) => url.endsWith('.svg');

  if (isSvg(media.sourceUrl)) {
    return (
      <Image
        src={media.sourceUrl}
        alt={media.altText ?? ''}
        width={500}
        height={500}
        className={className ?? ''}
      />
    );
  }

  // Get desktop and mobile variations
  const desktopSize = config?.desktop ? getSize(config.desktop) : null;
  const mobileSize = config?.mobile ? getSize(config.mobile) : null;

  // Fallback to original dimensions if no size found
  const width = desktopSize?.width ?? media.mediaDetails?.width ?? 1280;
  const height = desktopSize?.height ?? media.mediaDetails?.height ?? 720;

  // Use size URLs or fallback to original
  const desktopUrl = desktopSize?.sourceUrl ?? media.sourceUrl;
  const mobileUrl = mobileSize?.sourceUrl ?? desktopUrl;

  if (mobileUrl !== desktopUrl) {
    return (
      <picture>
        <source
          media="(max-width: 640px)"
          srcSet={mobileUrl}
        />
        <Image
          src={desktopUrl}
          alt={media.altText ?? ''}
          width={width}
          height={height}
          className={className ?? ''}
          sizes={config?.sizes ?? defaultSizes}
          quality={75}
        />
      </picture>
    );
  }

  return (
    <Image
      src={desktopUrl}
      alt={media.altText ?? ''}
      width={width}
      height={height}
      className={className ?? ''}
      sizes={config?.sizes ?? defaultSizes}
      quality={75}
    />
  );
};
