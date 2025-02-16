import Image from 'next/image';

interface MediaDetails {
  width: number;
  height: number;
}

export interface WordPressImage {
  sourceUrl: string;
  altText?: string;
  mediaDetails?: MediaDetails;
}

const defaultSizes = '(max-width: 640px) 640px, (max-width: 1280px) 1280px, 2560px';

export const getImage = (
  media: WordPressImage | null | undefined,
  className?: string,
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

  // Get dimensions from mediaDetails or use defaults
  const width = media.mediaDetails?.width ?? 1920;
  const height = media.mediaDetails?.height ?? 1080;

  return (
    <Image
      src={media.sourceUrl}
      alt={media.altText ?? ''}
      width={width}
      height={height}
      className={className ?? ''}
      sizes={sizes ?? defaultSizes}
      quality={90}
      priority={true}
    />
  );
};
