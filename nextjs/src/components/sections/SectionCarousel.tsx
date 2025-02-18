import Carousel from '@/components/carousel/Carousel';
import { getImage } from '@/components/helpers/Utilities';

export interface CarouselSection {
  type: 'carousel';
  title?: string;
  carouselItems: Array<{
    media: {
      sourceUrl: string;
    };
    title: string;
    summary: string;
  }>;
}

export default function SectionCarousel({ section }: { section: CarouselSection }) {
  const items = section.carouselItems.map(item => {
    const itemMedia = item.media ? getImage(
      item.media,
      'w-full h-full object-cover',
      ['i169medium', 'i169large']
    ) : undefined;

    return {
      media: itemMedia,
      title: item.title,
      summary: item.summary,
    };
  });

  return (
    <div className="container mx-auto px-4 my-25">
      <Carousel items={items} />
    </div>
  );
}

// GraphQL fragment for carousel sections
export const carouselSectionFragment = `
  fragment CarouselSection on LandingSection {
    type
    title
    carouselItems {
      media {
        sourceUrl
      }
      title
      summary
    }
  }
`;
