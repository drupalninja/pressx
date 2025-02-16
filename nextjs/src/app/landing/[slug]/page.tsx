import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import Hero from '@/components/hero/Hero';

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
          heroLayout
          media
          heading
          summary
          link {
            url
            title
          }
          link2 {
            url
            title
          }
          modifier
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
      heroLayout: 'image_top' | 'image_bottom' | 'image_bottom_split';
      media: React.ReactNode;
      heading: string;
      summary: string;
      link: {
        url: string;
        title: string;
      };
      link2?: {
        url: string;
        title: string;
      };
      modifier?: string;
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
              return (
                <Hero
                  key={index}
                  heroLayout={section.heroLayout}
                  media={section.media}
                  heading={section.heading}
                  summary={section.summary}
                  link={section.link}
                  link2={section.link2}
                  modifier={section.modifier}
                />
              );
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
