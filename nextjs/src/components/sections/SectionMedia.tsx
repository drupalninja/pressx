import Media from '@/components/media/Media';
import { getImage } from '@/components/helpers/Utilities';

export interface MediaSection {
  type: 'media';
  title?: string;
  media: {
    sourceUrl: string;
  };
}

export default function SectionMedia({ section }: { section: MediaSection }) {
  const mediaElement = section.media ? getImage(
    section.media,
    'w-full h-full object-cover',
    ['i169medium', 'i169large']
  ) : null;

  return (
    <div className={`container mx-auto px-4 my-6 lg:my-25`}>
      <div className="w-full">
        <Media
          media={mediaElement}
        />
      </div>
    </div>
  );
}

// GraphQL fragment for media sections
export const mediaSectionFragment = `
  fragment MediaSection on LandingSection {
    type
    title
    media {
      sourceUrl
    }
  }
`;
