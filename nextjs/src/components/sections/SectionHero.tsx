import Image from 'next/image';
import Hero from '@/components/hero/Hero';

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
  modifier?: string;
}

export default function SectionHero({ section, priority = false }: { section: HeroSection; priority?: boolean }) {
  const media = section.media ? (
    <Image
      src={section.media}
      alt=""
      width={1280}
      height={720}
      className="w-full h-auto"
      sizes="(max-width: 640px) 640px, (max-width: 960px) 960px, 1280px"
      priority={priority}
    />
  ) : null;

  return (
    <Hero
      heroLayout={section.heroLayout}
      media={media}
      heading={section.heading}
      summary={section.summary}
      link={section.link}
      link2={section.link2}
      modifier={section.modifier}
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
    modifier
  }
`;
