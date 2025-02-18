import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import SectionHero, { HeroSection, heroSectionFragment } from '@/components/sections/SectionHero';
import SectionAccordion, { AccordionSection, accordionSectionFragment } from '@/components/sections/SectionAccordion';
import SectionCardGroup, { CardGroupSection, cardGroupSectionFragment } from '@/components/sections/SectionCardGroup';
import SectionCarousel, { CarouselSection, carouselSectionFragment } from '@/components/sections/SectionCarousel';
import SectionEmbed, { EmbedSection, embedSectionFragment } from '@/components/sections/SectionEmbed';
import SectionGallery, { GallerySection, gallerySectionFragment } from '@/components/sections/SectionGallery';

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Array<HeroSection | AccordionSection | CardGroupSection | CarouselSection | EmbedSection | GallerySection>;
  };
}

const getLandingPageQuery = `
  ${heroSectionFragment}
  ${accordionSectionFragment}
  ${cardGroupSectionFragment}
  ${carouselSectionFragment}
  ${embedSectionFragment}
  ${gallerySectionFragment}
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
