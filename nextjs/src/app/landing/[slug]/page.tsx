import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import SectionHero, { HeroSection, heroSectionFragment } from '@/components/sections/SectionHero';

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Array<HeroSection>; // For now we only have hero sections
  };
}

const getLandingPageQuery = `
  ${heroSectionFragment}

  query GetLandingPage($slug: ID!) {
    landing(id: $slug, idType: SLUG) {
      title
      databaseId
      sections {
        ...HeroSection
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
    const data = await graphQLClient.request<LandingPageData>(
      getLandingPageQuery,
      { slug }
    );

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
                section={section}
                priority={index === 0}
              />
            );
          }
          return null;
        })}
      </main>
    );
  } catch (error) {
    console.error('Error fetching landing page:', error);
    notFound();
  }
}
