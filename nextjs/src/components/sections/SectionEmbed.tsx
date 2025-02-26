import Embed from '@/components/embed/Embed';

export interface EmbedSection {
  type: 'embed';
  title?: string;
  script: string;
  caption?: string;
  maxWidth?: string;
}

export default function SectionEmbed({ section }: { section: EmbedSection }) {
  // Use the script directly as content
  const content = section.script;

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
    script
    caption
    maxWidth
  }
`;
