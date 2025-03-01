import { previewClient } from '@/lib/graphql';
import type { Post, Page as PageType } from '@/types/wordpress';
import { cookies } from 'next/headers';
import { Section, SectionResolver, sectionsFragment } from '@/components/sections/SectionResolver';
import Page from '@/components/Page';

// Define the Landing type
interface Landing {
  id: string;
  title: string;
  status: string;
  databaseId: number;
  sections: Section[];
}

// Query for regular posts
const postQuery = `
  query Post($id: ID!) {
    post(id: $id, idType: DATABASE_ID) {
      id
      title
      date
      content
      status
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
    }
  }
`;

// Query for pages
const pageQuery = `
  query Page($id: ID!) {
    page(id: $id, idType: DATABASE_ID) {
      id
      title
      date
      content
      status
      databaseId
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
    }
  }
`;

// Query for landing pages
const landingQuery = `
  ${sectionsFragment}
  query Landing($id: ID!) {
    landing(id: $id, idType: DATABASE_ID) {
      id
      title
      status
      databaseId
      sections {
        ...Sections
      }
    }
  }
`;

export default async function PreviewPage({
  params: { id },
}: {
  params: { id: string };
}) {
  try {
    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    // First try to fetch as a post
    let postData: { post: Post } | null = null;
    let pageData: { page: PageType } | null = null;
    let landingData: { landing: Landing } | null = null;
    let contentType: 'post' | 'page' | 'landing' | null = null;

    try {
      // Try to fetch as a regular post first
      postData = await previewClient.request<{ post: Post }>(postQuery, { id });
      if (postData?.post) {
        contentType = 'post';
      }
    } catch (postError) {
      // If post fetch fails, we'll try page next
    }

    // If not a post, try as a page
    if (!postData?.post) {
      try {
        pageData = await previewClient.request<{ page: PageType }>(pageQuery, { id });
        if (pageData?.page) {
          contentType = 'page';
        }
      } catch (pageError) {
        // If page fetch fails, we'll try landing page next
      }
    }

    // If not a post or page, try as a landing page
    if (!postData?.post && !pageData?.page) {
      try {
        landingData = await previewClient.request<{ landing: Landing }>(landingQuery, { id });
        if (landingData?.landing) {
          contentType = 'landing';
        }
      } catch (landingError) {
        // Content not found as either post, page, or landing page
      }
    }

    // If neither post, page, nor landing page was found
    if (!postData?.post && !pageData?.page && !landingData?.landing) {
      return (
        <main className="flex min-h-screen flex-col items-center justify-between p-24">
          <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
            <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
              <p className="font-bold">Error</p>
              <p>Content with ID {id} not found. Make sure you're using the correct ID and that preview mode is enabled.</p>
              <p className="mt-2">Check that the WordPress GraphQL API is properly configured to allow access to private content.</p>
              <p className="mt-2">JWT Authentication Status: {jwtToken ? 'Token Present' : 'No Token Found'}</p>
            </div>
          </div>
        </main>
      );
    }

    // Render post preview
    if (contentType === 'post' && postData?.post) {
      const post = postData.post;

      return (
        <main className="flex min-h-screen flex-col items-center justify-between p-24" data-post-id={id} data-post-type="post">
          <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
            <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
              <p className="font-bold">Preview Mode</p>
              <p>This is a preview of your post. It may not reflect the final published version.</p>
              <p className="mt-2">Post Status: <span className="font-semibold">{post.status}</span></p>
              <p className="mt-2">Post ID: <span className="font-semibold">{id}</span></p>
              <p className="mt-2">Authentication: <span className="font-semibold">{jwtToken ? 'JWT Authenticated' : 'Not Authenticated'}</span></p>
            </div>

            <article className="p-6 bg-white rounded-lg shadow">
              {post.featuredImage && (
                <img
                  src={post.featuredImage.node.sourceUrl}
                  alt={post.featuredImage.node.altText}
                  className="w-full h-auto mb-4 rounded"
                />
              )}
              <h1 className="text-4xl font-bold mb-2">{post.title}</h1>
              <div className="text-gray-600 mb-4">
                {new Date(post.date).toLocaleDateString()}
              </div>
              <div
                className="prose max-w-none"
                dangerouslySetInnerHTML={{ __html: post.content }}
              />
            </article>
          </div>
        </main>
      );
    }

    // Render page preview
    if (contentType === 'page' && pageData?.page) {
      const page = pageData.page;

      return (
        <div>
          <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
            <p className="font-bold">Preview Mode</p>
            <p>This is a preview of your page. It may not reflect the final published version.</p>
            <p className="mt-2">Page Status: <span className="font-semibold">{page.status}</span></p>
            <p className="mt-2">Page ID: <span className="font-semibold">{id}</span></p>
            <p className="mt-2">Authentication: <span className="font-semibold">{jwtToken ? 'JWT Authenticated' : 'Not Authenticated'}</span></p>
          </div>

          <Page page={page} />
        </div>
      );
    }

    // Render landing page preview
    if (contentType === 'landing' && landingData?.landing) {
      const landing = landingData.landing;

      return (
        <main className="min-h-screen" data-post-id={id} data-post-type="landing">
          <div className="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
            <p className="font-bold">Preview Mode</p>
            <p>This is a preview of your landing page. It may not reflect the final published version.</p>
            <p className="mt-2">Page Status: <span className="font-semibold">{landing.status}</span></p>
            <p className="mt-2">Page ID: <span className="font-semibold">{id}</span></p>
            <p className="mt-2">Authentication: <span className="font-semibold">{jwtToken ? 'JWT Authenticated' : 'Not Authenticated'}</span></p>
          </div>

          {landing.sections?.map((section: Section, index: number) => (
            <SectionResolver key={index} section={section} />
          ))}
        </main>
      );
    }

  } catch (error) {
    console.error('Error fetching content:', error);

    // Check if we have a JWT token in cookies
    const cookieStore = cookies();
    const jwtToken = cookieStore.get('wp_jwt_token')?.value;

    return (
      <main className="flex min-h-screen flex-col items-center justify-between p-24">
        <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm">
          <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8" role="alert">
            <p className="font-bold">Error</p>
            <p>Failed to fetch content with ID {id}. Make sure preview mode is enabled and authentication is configured correctly.</p>
            <p className="mt-2">Error details: {error instanceof Error ? error.message : String(error)}</p>
            <p className="mt-2">JWT Authentication Status: {jwtToken ? 'Token Present' : 'No Token Found'}</p>
            <p className="mt-2">Check the browser console for more details.</p>
          </div>
        </div>
      </main>
    );
  }
}
