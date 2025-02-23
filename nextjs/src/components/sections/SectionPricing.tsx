import Pricing from '@/components/pricing/Pricing';

export interface PricingCard {
  eyebrow: string;
  title: string;
  monthlyLabel?: string;
  features: string[];
  ctaText: string;
  ctaLink: string;
}

export interface PricingSection {
  type: 'pricing';
  eyebrow: string;
  title: string;
  summary: string;
  includesLabel: string;
  pricingCards: PricingCard[];
}

export default function SectionPricing({ section }: { section: PricingSection }) {
  return (
    <Pricing
      eyebrow={section.eyebrow}
      title={section.title}
      summary={section.summary}
      includesLabel={section.includesLabel}
      cards={section.pricingCards}
    />
  );
}

// GraphQL fragment for pricing sections
export const pricingSectionFragment = `
  fragment PricingSection on LandingSection {
    type
    eyebrow
    title
    summary
    includesLabel
    pricingCards {
      eyebrow
      title
      monthlyLabel
      features
      ctaText
      ctaLink
    }
  }
`;
