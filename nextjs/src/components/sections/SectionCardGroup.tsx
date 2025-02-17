import CardGroup, { CustomCardProps } from '@/components/card-group/CardGroup';
import { StatCardProps } from '@/components/stat-card/StatCard';
import { getImage } from '@/components/helpers/Utilities';

export interface CardGroupSection {
  type: 'card_group';
  title?: string;
  cards: Array<{
    type: 'stat' | 'custom';
    media?: {
      sourceUrl: string;
      mediaDetails?: {
        width: number;
        height: number;
      };
    };
    mediaLink?: string;
    heading: {
      title: string;
      url?: string;
    };
    body?: string;
    summaryText?: string;
    tags?: string[];
    icon?: string;
    link?: {
      url: string;
      title: string;
    };
  }>;
}

export default function SectionCardGroup({ section }: { section: CardGroupSection }) {
  const cards = section.cards.map(card => {
    const cardMedia = card.media ? getImage(
      card.media,
      'w-full h-full object-cover',
      ['i169medium', 'i169large']
    ) : undefined;

    if (card.type === 'stat') {
      return {
        type: 'stat',
        media: cardMedia,
        heading: card.heading.title,
        body: card.body,
        icon: card.icon,
      } as StatCardProps;
    }

    return {
      type: 'custom',
      media: cardMedia,
      mediaLink: card.mediaLink,
      heading: card.heading,
      tags: card.tags,
      summaryText: card.summaryText,
      link: card.link,
    } as CustomCardProps;
  });

  return (
    <CardGroup
      title={section.title}
      cards={cards}
    />
  );
}

// GraphQL fragment for card group sections
export const cardGroupSectionFragment = `
  fragment CardGroupSection on LandingSection {
    type
    title
    cards {
      type
      media {
        sourceUrl
        mediaDetails {
          width
          height
        }
      }
      mediaLink
      heading {
        title
        url
      }
      body
      summaryText
      tags
      icon
      link {
        url
        title
      }
    }
  }
`;
