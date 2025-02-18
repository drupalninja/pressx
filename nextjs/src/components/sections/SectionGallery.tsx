import Gallery from '@/components/gallery/Gallery';
import Image from 'next/image';
import { getImage } from '@/components/helpers/Utilities';

export interface GallerySection {
  type: 'gallery';
  title?: string;
  summary?: string;
  mediaItems: Array<{
    media: {
      sourceUrl: string;
    };
    alt?: string;
  }>;
}

export default function SectionGallery({ section }: { section: GallerySection }) {
  const mediaElements = section.mediaItems.map((item, index) => {
    const media = getImage(
      item.media,
      'w-full h-full object-cover',
      ['i169medium', 'i169large']
    );

    return media;
  });

  return (
    <div className='container my-6 my-lg-15'>
      <Gallery
      title={section.title}
      summary={section.summary}
      mediaItems={mediaElements}
      />
    </div>
  );
}

// GraphQL fragment for gallery sections
export const gallerySectionFragment = `
  fragment GallerySection on LandingSection {
    type
    title
    summary
    mediaItems {
      media {
        sourceUrl
      }
      alt
    }
  }
`;