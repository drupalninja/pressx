import Sidebyside, { BulletProps } from '@/components/sidebyside/Sidebyside';
import { getImage } from '@/components/helpers/Utilities';
import { StatCardProps } from '@/components/stat-card/StatCard';

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
    type: 'bullet' | 'stat';
    text?: string;
    icon?: string;
    title?: string;
    summary?: string;
    customIcon?: {
      sourceUrl: string;
      width?: number;
      height?: number;
      alt?: string;
    };
  }>;
}

export default function SectionSidebyside({ section }: { section: SidebysideSection }) {
  const media = section.media ? getImage(
    section.media,
    'w-full h-auto rounded-lg',
    ['i43medium', 'i43medium']
  ) : null;

  const features = section.features?.map(feature => {
    if (feature.type === 'bullet') {
      return {
        type: 'bullet' as const,
        icon: feature.icon || 'check',
        summary: feature.text || ''
      } as BulletProps;
    } else if (feature.type === 'stat') {
      return {
        type: 'stat' as const,
        heading: feature.title || '',
        body: feature.summary || '',
        icon: feature.icon,
        media: feature.customIcon ? getImage(
          feature.customIcon,
          'w-16 h-16 object-contain mx-auto',
          ['thumbnail', 'medium']
        ) : undefined,
        border: false,
        layout: 'left'
      } as StatCardProps;
    }
    return null;
  }).filter((f): f is BulletProps | StatCardProps => f !== null);

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
      type
      text
      icon
      title
      summary
      customIcon {
        sourceUrl
        width
        height
        alt
      }
    }
  }
`;
