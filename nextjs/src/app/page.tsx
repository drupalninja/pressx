import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { Section, SectionResolver, sectionsFragment } from '@/components/sections/SectionResolver';

interface HomepageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Section[];
  };
}

const getHomepageQuery = `
  ${sectionsFragment}
  query GetHomepage {
    landing(id: "home", idType: SLUG) {
      title
      databaseId
      sections {
        ...Sections
      }
    }
  }
`;

export default async function Homepage() {
  try {
    const data = await graphQLClient.request<HomepageData>(getHomepageQuery);

    if (!data?.landing) {
      notFound();
    }

    return (
      <main className="min-h-screen" data-post-id={data.landing.databaseId} data-post-type="landing">
        {data.landing.sections?.map((section, index) => (
          <SectionResolver key={index} section={section} />
        ))}
      </main>
    );
  } catch (error) {
    console.error('Error fetching homepage:', error);
    console.error('Error details:', {
      message: error instanceof Error ? error.message : 'Unknown error',
      stack: error instanceof Error ? error.stack : undefined
    });
    notFound();
  }
}
