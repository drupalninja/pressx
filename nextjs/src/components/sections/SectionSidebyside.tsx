import Sidebyside, { BulletProps } from '@/components/sidebyside/Sidebyside';
import { getImage } from '@/components/helpers/Utilities';

export interface SidebysideSection {
  type: 'sidebyside';
  eyebrow?: string;
  layout?: 'image_left' | 'image_right';
  title: string;
  summary?: string;
  link?: {
    url: string;
    title: string;
  };
  media: {
    sourceUrl: string;
    width?: number;
    height?: number;
    alt?: string;
  };
  features?: Array<{
    text: string;
  }>;
}

export default function SectionSidebyside({ section }: { section: SidebysideSection }) {
  const media = section.media ? getImage(
    section.media,
    'w-full h-auto',
    ['i43medium', 'i43large']
  ) : null;

  const features = section.features?.map(feature => ({
    type: 'bullet' as const,
    icon: 'check',
    summary: feature.text
  }));

  return (
    <Sidebyside
      eyebrow={section.eyebrow}
      layout={section.layout === 'image_right' ? 'right' : 'left'}
      title={section.title}
      summary={section.summary}
      link={section.link}
      media={media}
      features={features}
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
    summary
    link {
      url
      title
    }
    media {
      sourceUrl
      width
      height
      alt
    }
    features {
      text
    }
  }
`;
