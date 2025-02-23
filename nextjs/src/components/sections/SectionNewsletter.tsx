import Newsletter from '@/components/newsletter/Newsletter';

export interface NewsletterSection {
  type: 'newsletter';
  title: string;
  summary: string;
}

export default function SectionNewsletter({ section }: { section: NewsletterSection }) {
  return (
    <Newsletter
      title={section.title}
      summary={section.summary}
    />
  );
}

// GraphQL fragment for newsletter sections
export const newsletterSectionFragment = `
  fragment NewsletterSection on LandingSection {
    type
    title
    summary
  }
`;
