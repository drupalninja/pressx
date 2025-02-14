import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { Hero } from '@/components/hero/Hero';

const getQueryForId = (id: string) => {
  const isNumeric = /^\d+$/.test(id);
  return `
    query LandingPage($id: ID!) {
      landing(id: $id, idType: ${isNumeric ? 'DATABASE_ID' : 'SLUG'}) {
        databaseId
        title
        slug
        sections {
          type
          title
          description
          backgroundImage
          ctaText
          ctaLink
        }
      }
    }
  `;
};

interface LandingPageData {
  landing: {
    databaseId: number;
    title: string;
    slug: string;
    sections: Array<{
      type: string;
      title: string;
      description: string;
      backgroundImage: string;
      ctaText: string;
      ctaLink: string;
    }>;
  };
}

export default async function LandingPage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  try {
    // Remove any leading/trailing slashes and decode the slug
    const cleanSlug = decodeURIComponent(slug.replace(/^\/+|\/+$/g, ''));
    const query = getQueryForId(cleanSlug);
    const data = await graphQLClient.request<LandingPageData>(query, { id: cleanSlug });
    console.log('GraphQL Response:', data); // Add debugging

    if (!data.landing) {
      notFound();
    }

    return (
      <main className="min-h-screen">
        {data.landing.sections?.map((section, index) => {
          switch (section.type) {
            case 'hero':
              return <Hero key={index} {...section} />;
            default:
              return null;
          }
        })}
      </main>
    );
  } catch (error) {
    console.error('Error fetching landing page:', error);
    notFound();
  }
}
