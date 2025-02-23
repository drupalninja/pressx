import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import SectionHero, { HeroSection, heroSectionFragment } from '@/components/sections/SectionHero';
import SectionAccordion, { AccordionSection, accordionSectionFragment } from '@/components/sections/SectionAccordion';
import SectionCardGroup, { CardGroupSection, cardGroupSectionFragment } from '@/components/sections/SectionCardGroup';
import SectionCarousel, { CarouselSection, carouselSectionFragment } from '@/components/sections/SectionCarousel';
import SectionEmbed, { EmbedSection, embedSectionFragment } from '@/components/sections/SectionEmbed';
import SectionGallery, { GallerySection, gallerySectionFragment } from '@/components/sections/SectionGallery';
import SectionLogoCollection, { SectionLogoCollectionProps } from '@/components/sections/SectionLogoCollection';
import SectionMedia, { MediaSection, mediaSectionFragment } from '@/components/sections/SectionMedia';
import SectionNewsletter, { NewsletterSection, newsletterSectionFragment } from '@/components/sections/SectionNewsletter';
import SectionPricing, { PricingSection, pricingSectionFragment } from '@/components/sections/SectionPricing';
import SectionQuote, { QuoteSection, quoteSectionFragment } from '@/components/sections/SectionQuote';
import SectionSidebyside, { SidebysideSection, sidebysideSectionFragment } from '@/components/sections/SectionSidebyside';
import SectionText, { TextSection, textSectionFragment } from '@/components/sections/SectionText';

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

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Array<HeroSection | AccordionSection | CardGroupSection | CarouselSection | EmbedSection | GallerySection | LogoCollectionSection | MediaSection | NewsletterSection | PricingSection | QuoteSection | SidebysideSection | TextSection>;
  };
}

const getLandingPageQuery = `
  ${heroSectionFragment}
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
  ${textSectionFragment}
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
  query GetLandingPage($slug: ID!) {
    landing(id: $slug, idType: SLUG) {
      title
      databaseId
      sections {
        ...HeroSection
        ...AccordionSection
        ...CardGroupSection
        ...CarouselSection
        ...EmbedSection
        ...GallerySection
        ...MediaSection
        ...LogoCollectionSection
        ...NewsletterSection
        ...PricingSection
        ...QuoteSection
        ...SidebysideSection
        ...TextSection
      }
    }
  }
`;

export default async function LandingPage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  try {
    console.log('Fetching landing page with slug:', slug);
    console.log('GraphQL endpoint:', process.env.NEXT_PUBLIC_WORDPRESS_API_URL);

    const data = await graphQLClient.request<LandingPageData>(
      getLandingPageQuery,
      { slug }
    );

    console.log('GraphQL response:', JSON.stringify(data, null, 2));

    if (!data?.landing) {
      console.log('No landing page found in response');
      notFound();
    }

    return (
      <main className="min-h-screen">
        {data.landing.sections?.map((section, index) => {
          if (section.type === 'hero') {
            return (
              <SectionHero
                key={index}
                section={section as HeroSection}
              />
            );
          }
          if (section.type === 'accordion') {
            return (
              <SectionAccordion
                key={index}
                section={section as AccordionSection}
              />
            );
          }
          if (section.type === 'card_group') {
            return (
              <SectionCardGroup
                key={index}
                section={section as CardGroupSection}
              />
            );
          }
          if (section.type === 'carousel') {
            return (
              <SectionCarousel
                key={index}
                section={section as CarouselSection}
              />
            );
          }
          if (section.type === 'embed') {
            return (
              <SectionEmbed
                key={index}
                section={section as EmbedSection}
              />
            );
          }
          if (section.type === 'gallery') {
            return (
              <SectionGallery
                key={index}
                section={section as GallerySection}
              />
            );
          }
          if (section.type === 'media') {
            return (
              <SectionMedia
                key={index}
                section={section as MediaSection}
              />
            );
          }
          if (section.type === 'logo_collection') {
            return (
              <SectionLogoCollection
                key={index}
                title={section.title}
                logos={section.logos}
              />
            );
          }
          if (section.type === 'newsletter') {
            return (
              <SectionNewsletter
                key={index}
                section={section as NewsletterSection}
              />
            );
          }
          if (section.type === 'pricing') {
            return (
              <SectionPricing
                key={index}
                section={section as PricingSection}
              />
            );
          }
          if (section.type === 'quote') {
            return (
              <SectionQuote
                key={index}
                section={section as QuoteSection}
              />
            );
          }
          if (section.type === 'sidebyside') {
            return (
              <SectionSidebyside
                key={index}
                section={section as SidebysideSection}
              />
            );
          }
          if (section.type === 'text') {
            return (
              <SectionText
                key={index}
                section={section as TextSection}
              />
            );
          }
          return null;
        })}
      </main>
    );
  } catch (error) {
    console.error('Error fetching landing page:', error);
    console.error('Error details:', {
      message: error instanceof Error ? error.message : 'Unknown error',
      stack: error instanceof Error ? error.stack : undefined
    });
    notFound();
  }
}
