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
                section={section}
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
