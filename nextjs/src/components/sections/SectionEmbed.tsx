import Embed from '@/components/embed/Embed';

export interface EmbedSection {
  type: 'embed';
  title?: string;
  embedUrl: string;
  caption?: string;
  maxWidth?: string;
}

export default function SectionEmbed({ section }: { section: EmbedSection }) {
  // Convert embedUrl to content for compatibility with Embed component
  const content = `<iframe src="${section.embedUrl}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; aspect-ratio:16/9;"></iframe>`;
  
  // If caption exists, add it to the content
  const fullContent = section.caption 
    ? `${content}<p class="mt-2 text-sm text-gray-600">${section.caption}</p>`
    : content;

  return (
    <Embed
      title={section.title}
      content={fullContent}
      modifier={section.maxWidth ? `my-6 lg:my-25 max-w-[${section.maxWidth}] mx-auto` : undefined}
    />
  );
}

// GraphQL fragment for embed sections
export const embedSectionFragment = `
  fragment EmbedSection on LandingSection {
    type
    title
    embedUrl
    caption
    maxWidth
  }
`;