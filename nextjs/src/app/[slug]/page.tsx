import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { Section, SectionResolver, sectionsFragment } from '@/components/sections/SectionResolver';

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Section[];
  };
}

const getLandingPageQuery = `
  ${sectionsFragment}
  query GetLandingPage($slug: ID!) {
    landing(id: $slug, idType: SLUG) {
      title
      databaseId
      sections {
        ...Sections
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
    const data = await graphQLClient.request<LandingPageData>(
      getLandingPageQuery,
      { slug }
    );

    if (!data?.landing) {
      notFound();
    }

    return (
      <main className="min-h-screen">
        {data.landing.sections?.map((section, index) => (
          <SectionResolver key={index} section={section} />
        ))}
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
