import Hero from '@/components/hero/Hero';
import { getImage } from '@/components/helpers/Utilities';

export interface HeroSection {
  type: 'hero';
  heroLayout: 'image_top' | 'image_bottom' | 'image_bottom_split';
  heading: string;
  summary: string;
  media: {
    sourceUrl: string;
    mediaDetails?: {
      width: number;
      height: number;
      sizes: Array<{
        name: string;
        sourceUrl: string;
        width: number;
        height: number;
      }>;
    };
  };
  link: {
    url: string;
    title: string;
  };
  link2?: {
    url: string;
    title: string;
  };
}

export default function SectionHero({ section }: { section: HeroSection }) {
  console.log('Hero section:', section);
  console.log('Media object:', section.media);
  console.log('Media source URL:', section.media?.sourceUrl);
  console.log('Media details:', section.media?.mediaDetails);
  console.log('Available sizes:', section.media?.mediaDetails?.sizes);

  const media = section.media ? getImage(
    section.media,
    'max-w-full h-auto',
    ['hero-s', 'hero-lx2']
  ) : null;

  console.log('Generated media element:', media);

  return (
    <Hero
      heroLayout={section.heroLayout}
      media={media}
      heading={section.heading}
      summary={section.summary}
      link={section.link}
      link2={section.link2}
    />
  );
}

// GraphQL fragment for hero sections
export const heroSectionFragment = `
  fragment HeroSection on LandingSection {
    type
    heroLayout
    heading
    summary
    media {
      sourceUrl
      mediaDetails {
        width
        height
        sizes {
          name
          sourceUrl
          width
          height
        }
      }
    }
    link {
      url
      title
    }
    link2 {
      url
      title
    }
  }
`;
