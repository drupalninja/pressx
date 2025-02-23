import Text from '@/components/text/Text';

export interface TextSection {
  type: 'text';
  eyebrow?: string;
  title?: string;
  body?: string;
  textLayout?: 'default' | 'centered' | 'buttons-right';
  link?: {
    url: string;
    title: string;
  };
  link2?: {
    url: string;
    title: string;
  };
}

export default function SectionText({ section }: { section: TextSection }) {
  return (
    <Text
      eyebrow={section.eyebrow}
      title={section.title}
      body={section.body}
      textLayout={section.textLayout}
      linkFragment={section.link}
      linkFragment2={section.link2}
    />
  );
}

// GraphQL fragment for text sections
export const textSectionFragment = `
  fragment TextSection on LandingSection {
    type
    eyebrow
    title
    body
    textLayout
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
