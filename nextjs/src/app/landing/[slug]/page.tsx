import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
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

const getLandingPageQuery = `
  query GetLandingPage($slug: ID!) {
    landing(id: $slug, idType: SLUG) {
      title
      databaseId
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
    console.log('GraphQL Response:', JSON.stringify(data, null, 2));

    if (!data?.landing) {
      console.log('No landing page found in response');
      notFound();
    }

    return (
      <main className="min-h-screen">
        {data.landing.sections?.map((section, index) => {
          if (section.type === 'hero') {
            return (
              <section
                key={index}
                className="relative min-h-[60vh] flex items-center"
                style={{
                  backgroundImage: section.backgroundImage ? `url(${section.backgroundImage})` : undefined,
                  backgroundSize: 'cover',
                  backgroundPosition: 'center',
                }}
              >
                <div className="container mx-auto px-4 py-12">
                  <div className="max-w-2xl">
                    <h1 className="text-4xl font-bold mb-4">{section.title}</h1>
                    <p className="text-xl mb-8">{section.description}</p>
                    {section.ctaText && section.ctaLink && (
                      <a
                        href={section.ctaLink}
                        className="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
                      >
                        {section.ctaText}
                      </a>
                    )}
                  </div>
                </div>
              </section>
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
