import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { getImage } from '@/components/helpers/Utilities';
import { Metadata } from 'next';
import { Section, SectionResolver, sectionsFragment } from '@/components/sections/SectionResolver';

interface Page {
  title: string;
  content: string;
  date: string;
  databaseId: number;
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText?: string;
      mediaDetails?: {
        width: number;
        height: number;
      };
    };
  };
}

interface Landing {
  title: string;
  databaseId: number;
  sections: Section[];
}

interface PagePageData {
  page: Page | null;
}

interface LandingPageData {
  landing: Landing | null;
}

const getPageQuery = `
  query GetPage($slug: ID!) {
    page(id: $slug, idType: URI) {
      title
      content
      date
      databaseId
      featuredImage {
        node {
          sourceUrl
          altText
          mediaDetails {
            width
            height
          }
        }
      }
    }
  }
`;

const getLandingQuery = `
  ${sectionsFragment}
  query GetLanding($slug: ID!) {
    landing(id: $slug, idType: URI) {
      title
      databaseId
      sections {
        ...Sections
      }
    }
  }
`;

export default async function SlugPage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  try {
    // First try to fetch as a regular page
    const pageData = await graphQLClient.request<PagePageData>(getPageQuery, { slug });

    if (pageData?.page) {
      const { page } = pageData;
      const featuredImage = page.featuredImage?.node
        ? getImage(
          {
            sourceUrl: page.featuredImage.node.sourceUrl,
            altText: page.featuredImage.node.altText,
          },
          'w-full h-full object-cover',
          'i169large'
        )
        : null;

      return (
        <article className="mb-8" data-page-id={page.databaseId}>
          <div className="mx-auto max-w-7xl p-4 sm:px-6 lg:px-8">
            {featuredImage && (
              <div className="relative aspect-[16/9] mb-6">
                {featuredImage}
              </div>
            )}
            <div className="mx-auto max-w-2xl">
              <h1 className="text-4xl font-bold mb-8">{page.title}</h1>
              <div
                className="prose prose-lg"
                dangerouslySetInnerHTML={{ __html: page.content }}
              />
            </div>
          </div>
        </article>
      );
    }

    // If not a regular page, try as a landing page
    const landingData = await graphQLClient.request<LandingPageData>(getLandingQuery, { slug });

    if (landingData?.landing) {
      const { landing } = landingData;

      return (
        <main className="min-h-screen" data-post-id={landing.databaseId} data-post-type="landing">
          {landing.sections?.map((section, index) => (
            <SectionResolver key={index} section={section} />
          ))}
        </main>
      );
    }

    // If neither page nor landing page was found
    notFound();
  } catch (error) {
    console.error('Error fetching content for slug:', slug, error);
    notFound();
  }
}

export async function generateMetadata({
  params: { slug },
}: {
  params: { slug: string };
}): Promise<Metadata> {
  try {
    // First try to get metadata from page
    const pageData = await graphQLClient.request<PagePageData>(getPageQuery, { slug });

    if (pageData?.page?.title) {
      return {
        title: pageData.page.title,
      };
    }

    // If not found, try landing page
    const landingData = await graphQLClient.request<LandingPageData>(getLandingQuery, { slug });

    if (landingData?.landing?.title) {
      return {
        title: landingData.landing.title,
      };
    }

    return {
      title: slug,
    };
  } catch (error) {
    return {
      title: slug,
    };
  }
}
