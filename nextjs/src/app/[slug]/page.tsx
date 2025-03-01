import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { getImage } from '@/components/helpers/Utilities';
import { Metadata } from 'next';

interface Page {
  title: string;
  content: string;
  excerpt: string;
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

interface PagePageData {
  page: Page | null;
}

const getPageQuery = `
  query GetPage($slug: ID!) {
    page(id: $slug, idType: SLUG) {
      title
      content
      excerpt
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

export default async function PagePage({
  params: { slug },
}: {
  params: { slug: string };
}) {
  const data = await graphQLClient.request<PagePageData>(getPageQuery, { slug });

  if (!data?.page) {
    notFound();
  }

  const { page } = data;
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
          {page.excerpt && (
            <div
              className="prose prose-lg lead mb-4"
              dangerouslySetInnerHTML={{ __html: page.excerpt }}
            />
          )}
          <div
            className="prose prose-lg"
            dangerouslySetInnerHTML={{ __html: page.content }}
          />
        </div>
      </div>
    </article>
  );
}

export async function generateMetadata({
  params: { slug },
}: {
  params: { slug: string };
}): Promise<Metadata> {
  try {
    const data = await graphQLClient.request<PagePageData>(getPageQuery, { slug });

    return {
      title: data.page?.title || slug,
    };
  } catch (error) {
    return {
      title: slug,
    };
  }
}
