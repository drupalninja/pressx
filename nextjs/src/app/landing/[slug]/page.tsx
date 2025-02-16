import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import Hero from '@/components/hero/Hero';
import Image from 'next/image';

interface LandingPageData {
  landing: {
    title: string;
    databaseId: number;
    sections: Array<{
      type: string;
      heroLayout: string;
      heading: string;
      summary: string;
      media: string;
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

const getLandingPageQuery = `
  query GetLandingPage($slug: ID!) {
    landing(id: $slug, idType: SLUG) {
      title
      databaseId
      sections {
        type
        heroLayout
        heading
        summary
        media
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
            const media = section.media ? (
              <Image
                src={section.media}
                alt=""
                width={1280}
                height={720}
                className="w-full h-auto"
              />
            ) : null;

            return (
              <Hero
                key={index}
                heroLayout={section.heroLayout as 'image_top' | 'image_bottom' | 'image_bottom_split'}
                media={media}
                heading={section.heading}
                summary={section.summary}
                link={section.link}
                link2={section.link2}
                modifier={section.modifier}
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
