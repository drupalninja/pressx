import Sidebyside from '@/components/sidebyside/Sidebyside';
import Image from 'next/image';

export interface FeatureItem {
  text: string;
}

export interface SidebysideSection {
  type: 'sidebyside';
  eyebrow?: string;
  layout?: string;
  title: string;
  summary?: {
    value: string;
  };
  link?: {
    url: string;
    title: string;
  };
  media: {
    sourceUrl: string;
    alt?: string;
    width?: number;
    height?: number;
  };
  modifier?: string;
  features?: FeatureItem[];
}

interface SidebysideProps {
  eyebrow?: string;
  layout?: string;
  title: string;
  summary?: { __html: string };
  link?: {
    url: string;
    title: string;
  };
  media?: React.ReactNode;
  modifier?: string;
  features?: FeatureItem[];
}

export default function SectionSidebyside({ section }: { section: SidebysideSection }) {
  const media = section.media && (
    <Image
      src={section.media.sourceUrl}
      alt={section.media.alt || ''}
      width={section.media.width || 800}
      height={section.media.height || 600}
      className="w-full h-auto"
    />
  );

  return (
    <Sidebyside
      eyebrow={section.eyebrow}
      layout={section.layout}
      title={section.title}
      summary={section.summary?.value}
      link={section.link}
      media={media}
      modifier={section.modifier}
      features={section.features as any}
    />
  );
}

// GraphQL fragment for sidebyside sections
export const sidebysideSectionFragment = `
  fragment SidebysideSection on LandingSection {
    type
    eyebrow
    layout
    title
    summary {
      value
    }
    link {
      url
      title
    }
    media {
      sourceUrl
      alt
      width
      height
    }
    modifier
    features {
      text
    }
  }
`;
