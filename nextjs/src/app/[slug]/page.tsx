import { graphQLClient } from '@/lib/graphql';
import { notFound } from 'next/navigation';
import { Metadata } from 'next';
import { sectionsFragment } from '@/components/sections/SectionResolver';
import Page from '@/components/Page';
import Landing from '@/components/Landing';
import { PageResponse, LandingResponse } from '@/types/wordpress';

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
    const pageData = await graphQLClient.request<PageResponse>(getPageQuery, { slug });

    if (pageData?.page) {
      return <Page page={pageData.page} />;
    }

    // If not a regular page, try as a landing page
    const landingData = await graphQLClient.request<LandingResponse>(getLandingQuery, { slug });

    if (landingData?.landing) {
      return <Landing landing={landingData.landing as any} />;
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
    const pageData = await graphQLClient.request<PageResponse>(getPageQuery, { slug });

    if (pageData?.page?.title) {
      return {
        title: pageData.page.title,
      };
    }

    // If not found, try landing page
    const landingData = await graphQLClient.request<LandingResponse>(getLandingQuery, { slug });

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
