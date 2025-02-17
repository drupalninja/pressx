import Accordion from '@/components/accordion/Accordion';

export interface AccordionSection {
  type: 'accordion';
  title?: string;
  accordionItems: Array<{
    title: string;
    body: {
      value: string;
    };
    link?: {
      url: string;
      title: string;
    };
  }>;
}

export default function SectionAccordion({ section }: { section: AccordionSection }) {
  return (
    <Accordion
      title={section.title}
      items={section.accordionItems}
    />
  );
}

// GraphQL fragment for accordion sections
export const accordionSectionFragment = `
  fragment AccordionSection on LandingSection {
    type
    title
    accordionItems {
      title
      body {
        value
      }
      link {
        url
        title
      }
    }
  }
`;
