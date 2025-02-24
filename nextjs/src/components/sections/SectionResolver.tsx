import SectionHero, { HeroSection, heroSectionFragment } from './SectionHero';
import SectionText, { TextSection, textSectionFragment } from './SectionText';
import SectionAccordion, { AccordionSection, accordionSectionFragment } from './SectionAccordion';
import SectionCardGroup, { CardGroupSection, cardGroupSectionFragment } from './SectionCardGroup';
import SectionCarousel, { CarouselSection, carouselSectionFragment } from './SectionCarousel';
import SectionEmbed, { EmbedSection, embedSectionFragment } from './SectionEmbed';
import SectionGallery, { GallerySection, gallerySectionFragment } from './SectionGallery';
import SectionLogoCollection from './SectionLogoCollection';
import SectionMedia, { MediaSection, mediaSectionFragment } from './SectionMedia';
import SectionNewsletter, { NewsletterSection, newsletterSectionFragment } from './SectionNewsletter';
import SectionPricing, { PricingSection, pricingSectionFragment } from './SectionPricing';
import SectionQuote, { QuoteSection, quoteSectionFragment } from './SectionQuote';
import SectionSidebyside, { SidebysideSection, sidebysideSectionFragment } from './SectionSidebyside';
import SectionRecentPosts, { RecentPostsSection, recentPostsSectionFragment } from './SectionRecentPosts';

interface LogoCollectionSection {
  type: 'logo_collection';
  title: string;
  logos: Array<{
    sourceUrl: string;
    width?: number;
    height?: number;
    alt?: string;
  }>;
}

export type Section =
  | HeroSection
  | TextSection
  | AccordionSection
  | CardGroupSection
  | CarouselSection
  | EmbedSection
  | GallerySection
  | LogoCollectionSection
  | MediaSection
  | NewsletterSection
  | PricingSection
  | QuoteSection
  | SidebysideSection
  | RecentPostsSection;

export function SectionResolver({ section }: { section: Section }) {
  switch (section.type) {
    case 'hero':
      return <SectionHero section={section} />;
    case 'text':
      return <SectionText section={section} />;
    case 'accordion':
      return <SectionAccordion section={section} />;
    case 'card_group':
      return <SectionCardGroup section={section} />;
    case 'carousel':
      return <SectionCarousel section={section} />;
    case 'embed':
      return <SectionEmbed section={section} />;
    case 'gallery':
      return <SectionGallery section={section} />;
    case 'logo_collection':
      return <SectionLogoCollection title={section.title} logos={section.logos} />;
    case 'media':
      return <SectionMedia section={section} />;
    case 'newsletter':
      return <SectionNewsletter section={section} />;
    case 'pricing':
      return <SectionPricing section={section} />;
    case 'quote':
      return <SectionQuote section={section} />;
    case 'side_by_side':
      return <SectionSidebyside section={section} />;
    case 'recent_posts':
      return <SectionRecentPosts section={section} />;
    default:
      return null;
  }
}

export const sectionsFragment = `
  fragment LogoCollectionSection on LandingSection {
    type
    title
    logos {
      sourceUrl
      width
      height
      alt
    }
  }

  fragment Sections on LandingSection {
    ...HeroSection
    ...TextSection
    ...AccordionSection
    ...CardGroupSection
    ...CarouselSection
    ...EmbedSection
    ...GallerySection
    ...LogoCollectionSection
    ...MediaSection
    ...NewsletterSection
    ...PricingSection
    ...QuoteSection
    ...SidebysideSection
    ...RecentPostsSection
  }
  ${heroSectionFragment}
  ${textSectionFragment}
  ${accordionSectionFragment}
  ${cardGroupSectionFragment}
  ${carouselSectionFragment}
  ${embedSectionFragment}
  ${gallerySectionFragment}
  ${mediaSectionFragment}
  ${newsletterSectionFragment}
  ${pricingSectionFragment}
  ${quoteSectionFragment}
  ${sidebysideSectionFragment}
  ${recentPostsSectionFragment}
`;
