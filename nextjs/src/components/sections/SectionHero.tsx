import Hero from '@/components/hero/Hero';
import { getImage } from '@/components/helpers/Utilities';

export interface HeroSection {
  type: 'hero';
  heroLayout: 'image_top' | 'image_bottom' | 'image_bottom_split';
  heading: string;
  summary: string;
  media: string;
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
  const media = section.media ? getImage(
    { sourceUrl: section.media },
    'max-w-full h-auto',
    {
      mobile: 'hero-s',
      desktop: 'hero-l',
      sizes: '(max-width: 640px) 640px, (max-width: 960px) 960px, 1280px'
    }
  ) : null;

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
    media
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
