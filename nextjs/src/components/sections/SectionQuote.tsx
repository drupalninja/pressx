import Quote from '@/components/quote/Quote';

export interface QuoteSection {
  type: 'quote';
  quote: string;
  author: string;
  jobTitle?: string;
  media?: {
    sourceUrl: string;
    alt?: string;
    width?: number;
    height?: number;
  };
}

export default function SectionQuote({ section }: { section: QuoteSection }) {
  return (
    <div className={`container mx-auto my-6 lg:my-25`}>
      <div className="flex justify-center">
        <Quote
          quote={section.quote}
          author={section.author}
          jobTitle={section.jobTitle}
          thumb={section.media ? {
            image: {
              url: section.media.sourceUrl
            }
          } : undefined}
        />
      </div>
    </div>
  );
}

// GraphQL fragment for quote sections
export const quoteSectionFragment = `
  fragment QuoteSection on LandingSection {
    type
    quote
    author
    jobTitle
    media {
      sourceUrl
      alt
      width
      height
    }
  }
`;
