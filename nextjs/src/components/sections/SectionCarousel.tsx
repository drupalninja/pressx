import Carousel from '@/components/carousel/Carousel';
import { getImage } from '@/components/helpers/Utilities';

export interface CarouselSection {
  type: 'carousel';
  title?: string;
  items: Array<{
    media: {
      sourceUrl: string;
    };
    title: string;
    summary: string;
  }>;
}

export default function SectionCarousel({ section }: { section: CarouselSection }) {
  const items = section.items.map(item => ({
    media: item.media ? getImage(
      item.media,
      'max-w-full h-auto',
      ['i169medium', 'i169large']
    ) : null,
    title: item.title,
    summary: item.summary,
  }));

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
    items {
      media {
        sourceUrl
      }
      title
      summary
    }
  }
`;
